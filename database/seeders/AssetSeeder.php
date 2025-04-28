<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\Department;
use Carbon\Carbon;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = Department::all();
        
        // Computers
        $computers = [
            [
                'name' => 'Desktop PC 001',
                'type' => 'Desktop',
                'brand' => 'Dell',
                'model' => 'OptiPlex 7090',
                'serial' => 'DELL7090001',
                'status' => 'active',
            ],
            [
                'name' => 'Desktop PC 002',
                'type' => 'Desktop',
                'brand' => 'HP',
                'model' => 'EliteDesk 800 G6',
                'serial' => 'HP800G6002',
                'status' => 'active',
            ],
            [
                'name' => 'Laptop 001',
                'type' => 'Laptop',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad X1 Carbon',
                'serial' => 'LEN001X1C',
                'status' => 'active',
            ],
            [
                'name' => 'Laptop 002',
                'type' => 'Laptop',
                'brand' => 'Apple',
                'model' => 'MacBook Pro 16',
                'serial' => 'APPL002MBP',
                'status' => 'active',
            ],
        ];

        // Printers
        $printers = [
            [
                'name' => 'Printer 001',
                'type' => 'Printer',
                'brand' => 'HP',
                'model' => 'LaserJet Pro MFP M428fdw',
                'serial' => 'HPLJ001M428',
                'status' => 'active',
            ],
            [
                'name' => 'Printer 002',
                'type' => 'Printer',
                'brand' => 'Epson',
                'model' => 'EcoTank ET-4760',
                'serial' => 'EPS002ET4760',
                'status' => 'inactive',
            ],
        ];

        // Monitors
        $monitors = [
            [
                'name' => 'Monitor 001',
                'type' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'UltraSharp U2720Q',
                'serial' => 'DELL001U2720Q',
                'status' => 'active',
            ],
            [
                'name' => 'Monitor 002',
                'type' => 'Monitor',
                'brand' => 'LG',
                'model' => '27UL500-W',
                'serial' => 'LG00227UL500',
                'status' => 'active',
            ],
        ];

        // Servers
        $servers = [
            [
                'name' => 'Server 001',
                'type' => 'Server',
                'brand' => 'Dell',
                'model' => 'PowerEdge R740',
                'serial' => 'DELL001R740',
                'status' => 'active',
            ],
            [
                'name' => 'Server 002',
                'type' => 'Server',
                'brand' => 'HP',
                'model' => 'ProLiant DL380 Gen10',
                'serial' => 'HP002DL380G10',
                'status' => 'active',
            ],
        ];

        // Other devices
        $others = [
            [
                'name' => 'Network Switch 001',
                'type' => 'Network',
                'brand' => 'Cisco',
                'model' => 'Catalyst 9300',
                'serial' => 'CISCO001C9300',
                'status' => 'active',
            ],
            [
                'name' => 'Projector 001',
                'type' => 'Projector',
                'brand' => 'Epson',
                'model' => 'PowerLite 2250U',
                'serial' => 'EPS001P2250U',
                'status' => 'active',
            ],
        ];

        $allAssets = array_merge($computers, $printers, $monitors, $servers, $others);
        
        // Create assets and assign to departments
        foreach ($allAssets as $index => $assetData) {
            // Create a random OCS ID (simulating OCS Inventory integration)
            $ocsId = 'OCS-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            // Assign to a random department
            $departmentId = $departments->random()->id;
            
            // Create the asset
            Asset::create([
                'ocs_id' => $ocsId,
                'name' => $assetData['name'],
                'type' => $assetData['type'],
                'brand' => $assetData['brand'],
                'model' => $assetData['model'],
                'serial' => $assetData['serial'],
                'status' => $assetData['status'],
                'department_id' => $departmentId,
                'last_sync' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }
    }
}