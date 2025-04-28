<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\User;
use App\Models\Maintenance;
use App\Models\Certificate;
use App\Models\Signature;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get technicians
        $technicians = User::where('role_id', 2)->get();
        
        // If no technicians found, abort
        if ($technicians->isEmpty()) {
            $this->command->error('No technicians found. Please run UserSeeder first.');
            return;
        }
        
        // Get assets
        $assets = Asset::all();
        
        // If no assets found, abort
        if ($assets->isEmpty()) {
            $this->command->error('No assets found. Please run AssetSeeder first.');
            return;
        }
        
        // Create completed maintenances
        for ($i = 0; $i < 10; $i++) {
            // Get random asset and technician
            $asset = $assets->random();
            $technician = $technicians->random();
            
            // Create maintenance record
            $startDate = Carbon::now()->subDays(rand(30, 60));
            $endDate = (clone $startDate)->addHours(rand(1, 8));
            
            $maintenance = Maintenance::create([
                'asset_id' => $asset->id,
                'technician_id' => $technician->id,
                'maintenance_type' => collect(['preventive', 'corrective', 'predictive'])->random(),
                'diagnosis' => 'Maintenance diagnosis for ' . $asset->name . ': ' . $this->getRandomDiagnosis(),
                'procedure' => 'Maintenance procedure for ' . $asset->name . ': ' . $this->getRandomProcedure(),
                'status' => 'completed',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
            
            // Create certificate for some maintenances
            if (rand(0, 1)) {
                $code = 'CERT-' . $startDate->format('Ymd') . '-' . Str::padLeft($maintenance->id, 5, '0');
                $certificateDate = (clone $endDate)->addHours(rand(1, 24));
                
                $certificate = Certificate::create([
                    'maintenance_id' => $maintenance->id,
                    'code' => $code,
                    'status' => rand(0, 1) ? 'completed' : 'pending',
                    'file_path' => 'certificates/' . $code . '.pdf',
                    'generation_date' => $certificateDate,
                ]);
                
                // Add technician signature
                Signature::create([
                    'certificate_id' => $certificate->id,
                    'user_id' => $technician->id,
                    'signature_type' => 'technician',
                    'signature_date' => (clone $certificateDate)->addMinutes(rand(10, 60)),
                    'digital_signature' => $this->generateFakeSignatureData(),
                ]);
                
                // Add manager signature for some certificates
                if ($certificate->status === 'completed') {
                    $manager = User::where('id', $asset->department->manager_id)->first();
                    
                    if ($manager) {
                        Signature::create([
                            'certificate_id' => $certificate->id,
                            'user_id' => $manager->id,
                            'signature_type' => 'manager',
                            'signature_date' => (clone $certificateDate)->addHours(rand(1, 24)),
                            'digital_signature' => $this->generateFakeSignatureData(),
                        ]);
                    }
                }
            }
        }
        
        // Create in-progress maintenances
        for ($i = 0; $i < 5; $i++) {
            // Get random asset and technician
            $asset = $assets->random();
            $technician = $technicians->random();
            
            // Update asset status
            $asset->status = 'in_maintenance';
            $asset->save();
            
            // Create maintenance record
            $startDate = Carbon::now()->subDays(rand(0, 7));
            
            Maintenance::create([
                'asset_id' => $asset->id,
                'technician_id' => $technician->id,
                'maintenance_type' => collect(['preventive', 'corrective', 'predictive'])->random(),
                'diagnosis' => 'Ongoing maintenance diagnosis for ' . $asset->name . ': ' . $this->getRandomDiagnosis(),
                'procedure' => 'Ongoing maintenance procedure for ' . $asset->name . ': ' . $this->getRandomProcedure(),
                'status' => 'in_progress',
                'start_date' => $startDate,
                'end_date' => null,
            ]);
        }
    }
    
    /**
     * Generate random diagnosis text
     */
    private function getRandomDiagnosis()
    {
        $diagnoses = [
            'System running slow due to outdated drivers and excessive startup programs.',
            'Hard drive showing early signs of failure with bad sectors detected.',
            'Overheating issues caused by dust accumulation in cooling vents and heat sink.',
            'Display flickering due to loose connection or damaged cable.',
            'Network connectivity issues related to outdated network adapter drivers.',
            'Printer paper feed mechanism jammed with debris.',
            'System crashes related to memory module issues.',
            'Battery not holding charge, showing signs of degradation.',
            'Software conflicts causing application crashes.',
            'Operating system corruption requiring repair or reinstallation.',
        ];
        
        return $diagnoses[array_rand($diagnoses)];
    }
    
    /**
     * Generate random procedure text
     */
    private function getRandomProcedure()
    {
        $procedures = [
            'Performed full system cleanup, removed unnecessary startup items, and updated all drivers.',
            'Ran disk check and repair, backed up critical data, and scheduled hard drive replacement.',
            'Disassembled unit, cleaned all cooling components, applied new thermal paste, and tested temperature under load.',
            'Inspected and replaced faulty display cable, tested with different resolution settings.',
            'Updated network drivers, reset TCP/IP stack, and configured proper network settings.',
            'Disassembled paper feed mechanism, removed debris, lubricated moving parts, and calibrated sensors.',
            'Tested memory modules, identified and replaced faulty module, ran memory diagnostic tests.',
            'Tested battery capacity, reset battery controller, recommended replacement due to age and wear.',
            'Identified conflicting software, updated applications, adjusted compatibility settings.',
            'Performed system file check, repaired boot configuration, and reinstalled corrupted components.',
        ];
        
        return $procedures[array_rand($procedures)];
    }
    
    /**
     * Generate fake signature data (base64 encoded)
     */
    private function generateFakeSignatureData()
    {
        // This is a small transparent placeholder - in a real app, this would be actual signature data
        return 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
    }
}