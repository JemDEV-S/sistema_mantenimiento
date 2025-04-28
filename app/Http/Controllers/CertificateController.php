<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Maintenance;
use App\Models\Signature;
use App\Services\PdfService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    protected $pdfService;
    
    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }
    
    public function index(Request $request)
    {
        $query = Certificate::with(['maintenance.asset', 'maintenance.technician', 'signatures']);
        
        // Filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('code') && $request->code != '') {
            $query->where('code', 'like', "%{$request->code}%");
        }
        
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('generation_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('generation_date', '<=', $request->date_to);
        }
        
        $certificates = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('certificates.index', compact('certificates'));
    }
    
    public function show($id)
    {
        $certificate = Certificate::with(['maintenance.asset.department', 'maintenance.technician', 'signatures.user'])
            ->findOrFail($id);
            
        return view('certificates.show', compact('certificate'));
    }
    
    public function create($maintenance_id)
    {
        $maintenance = Maintenance::with(['asset.department', 'technician'])->findOrFail($maintenance_id);
        
        if ($maintenance->status !== 'completed') {
            return redirect()->route('maintenances.show', $maintenance->id)
                ->with('error', 'No se puede generar un acta para un mantenimiento no completado');
        }
        
        if ($maintenance->certificate) {
            return redirect()->route('certificates.show', $maintenance->certificate->id)
                ->with('info', 'Ya existe un acta para este mantenimiento');
        }
        
        return view('certificates.create', compact('maintenance'));
    }
    
    public function store(Request $request, $maintenance_id)
    {
        $maintenance = Maintenance::findOrFail($maintenance_id);
        
        if ($maintenance->status !== 'completed') {
            return redirect()->route('maintenances.show', $maintenance->id)
                ->with('error', 'No se puede generar un acta para un mantenimiento no completado');
        }
        
        if ($maintenance->certificate) {
            return redirect()->route('certificates.show', $maintenance->certificate->id)
                ->with('info', 'Ya existe un acta para este mantenimiento');
        }
        
        // Generate unique code for the certificate
        $code = 'CERT-' . date('Ymd') . '-' . Str::padLeft($maintenance->id, 5, '0');
        
        // Create certificate in database
        $certificate = Certificate::create([
            'maintenance_id' => $maintenance->id,
            'code' => $code,
            'status' => 'pending',
            'generation_date' => Carbon::now(),
        ]);
        
        // Generate PDF
        $pdfPath = $this->pdfService->generateMaintenanceCertificate($certificate);
        
        // Update file path
        $certificate->update(['file_path' => $pdfPath]);
        
        return redirect()->route('certificates.sign', $certificate->id)
            ->with('success', 'Acta generada correctamente. Por favor, proceda a firmarla.');
    }
    
    public function sign($id)
    {
        $certificate = Certificate::with(['maintenance.asset.department', 'maintenance.technician', 'signatures.user'])
            ->findOrFail($id);
            
        $user = Auth::user();
        $canSign = false;
        $signatureType = null;
        
        // Verify if user can sign
        if ($user->id === $certificate->maintenance->technician_id && !$certificate->isSignedByTechnician()) {
            $canSign = true;
            $signatureType = 'technician';
        } elseif ($user->isManager() && 
                 $user->department_id === $certificate->maintenance->asset->department_id && 
                 !$certificate->isSignedByManager()) {
            $canSign = true;
            $signatureType = 'manager';
        }
        
        return view('certificates.sign', compact('certificate', 'canSign', 'signatureType'));
    }
    
    public function registerSignature(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);
        $user = Auth::user();
        
        // Validate signature type
        $validated = $request->validate([
            'signature_type' => 'required|in:technician,manager',
            'digital_signature' => 'required|string',
        ]);
        
        // Verify permissions
        if ($validated['signature_type'] === 'technician' && $user->id !== $certificate->maintenance->technician_id) {
            return redirect()->route('certificates.sign', $certificate->id)
                ->with('error', 'No tiene permisos para firmar como técnico');
        }
        
        if ($validated['signature_type'] === 'manager' && 
            (!$user->isManager() || $user->department_id !== $certificate->maintenance->asset->department_id)) {
            return redirect()->route('certificates.sign', $certificate->id)
                ->with('error', 'No tiene permisos para firmar como jefe de departamento');
        }
        
        // Verify if a signature of this type already exists
        if ($certificate->signatures()->where('signature_type', $validated['signature_type'])->exists()) {
            return redirect()->route('certificates.sign', $certificate->id)
                ->with('error', 'Este documento ya tiene una firma de este tipo');
        }
        
        // Register signature
        Signature::create([
            'certificate_id' => $certificate->id,
            'user_id' => $user->id,
            'signature_type' => $validated['signature_type'],
            'signature_date' => Carbon::now(),
            'digital_signature' => $validated['digital_signature'],
        ]);
        
        // Update certificate status if all signatures are complete
        if ($certificate->signatures()->count() === 2) {
            $certificate->update(['status' => 'completed']);
            
            // Regenerate PDF with signatures
            $pdfPath = $this->pdfService->generateMaintenanceCertificateWithSignatures($certificate);
            $certificate->update(['file_path' => $pdfPath]);
        }
        
        return redirect()->route('certificates.show', $certificate->id)
            ->with('success', 'Firma registrada correctamente');
    }
    
    public function download($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        if (!$certificate->file_path || !Storage::exists($certificate->file_path)) {
            return redirect()->route('certificates.show', $certificate->id)
                ->with('error', 'El archivo del acta no está disponible');
        }
        
        return Storage::download($certificate->file_path, 'Acta-' . $certificate->code . '.pdf');
    }
}