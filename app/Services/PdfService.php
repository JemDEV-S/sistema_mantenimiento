<?php
// app/Services/PdfService.php
namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Generate maintenance certificate in PDF
     *
     * @param Certificate $certificate
     * @return string Path of the generated file
     */
    public function generateMaintenanceCertificate(Certificate $certificate)
    {
        $maintenance = $certificate->maintenance;
        $asset = $maintenance->asset;
        $technician = $maintenance->technician;
        $department = $asset->department;
        $manager = $department->manager;
        
        $data = [
            'certificate' => $certificate,
            'maintenance' => $maintenance,
            'asset' => $asset,
            'technician' => $technician,
            'department' => $department,
            'manager' => $manager,
            'date' => now()->format('d/m/Y'),
            'time' => now()->format('H:i'),
            'signatures' => false,
        ];
        
        $pdf = PDF::loadView('pdf.maintenance_certificate', $data);
        
        // Save PDF
        $path = 'certificates/' . $certificate->code . '.pdf';
        Storage::put($path, $pdf->output());
        
        return $path;
    }
    
    /**
     * Generate maintenance certificate with signatures in PDF
     *
     * @param Certificate $certificate
     * @return string Path of the generated file
     */
    public function generateMaintenanceCertificateWithSignatures(Certificate $certificate)
    {
        $maintenance = $certificate->maintenance;
        $asset = $maintenance->asset;
        $technician = $maintenance->technician;
        $department = $asset->department;
        $manager = $department->manager;
        
        // Get signatures
        $signatures = $certificate->signatures()->with('user')->get();
        $technicianSignature = $signatures->where('signature_type', 'technician')->first();
        $managerSignature = $signatures->where('signature_type', 'manager')->first();
        
        $data = [
            'certificate' => $certificate,
            'maintenance' => $maintenance,
            'asset' => $asset,
            'technician' => $technician,
            'department' => $department,
            'manager' => $manager,
            'date' => now()->format('d/m/Y'),
            'time' => now()->format('H:i'),
            'signatures' => true,
            'technicianSignature' => $technicianSignature,
            'managerSignature' => $managerSignature,
        ];
        
        $pdf = PDF::loadView('pdf.maintenance_certificate', $data);
        
        // Save signed PDF
        $path = 'certificates/' . $certificate->code . '_signed.pdf';
        Storage::put($path, $pdf->output());
        
        return $path;
    }
}