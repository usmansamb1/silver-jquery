<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Service extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'is_active',
        'service_type', // 'A' or 'B'
        'estimated_duration',
        'vat_percentage'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'vat_percentage' => 'decimal:2'
    ];

    public function bookings()
    {
        return $this->hasMany(ServiceBooking::class);
    }

    public function calculateTotalPrice()
    {
        $vatAmount = $this->base_price * ($this->vat_percentage / 100);
        return $this->base_price + $vatAmount;
    }

    /**
     * Get a service name by its ID
     *
     * @param string $serviceId
     * @return string
     */
    public static function getServiceNameById($serviceId)
    {
        $serviceNames = [
            'rfid_80mm' => 'RFID Chip for Trucks Size 80mm',
            'rfid_120mm' => 'RFID Chip for Trucks Size 120mm',
            'oil_change' => 'Oil Change Service',
        ];
        
        return $serviceNames[$serviceId] ?? 'Unknown Service';
    }

    /**
     * Get a service type by its ID
     *
     * @param string $serviceType
     * @return string
     */
    public static function getServiceTypeById($serviceType)
    {
        $serviceTypes = [
            'rfid_car' => 'RFID Chip for Cars',
            'rfid_truck' => 'RFID Chip for Trucks',
            'oil_change' => 'Oil Change Service',
        ];
        
        return $serviceTypes[$serviceType] ?? 'Unknown Service Type';
    }
    
    /**
     * Get a numeric service ID from its code string
     *
     * @param string $serviceCode
     * @return int
     */
    public static function getServiceIdFromCode($serviceCode)
    {
        // Try to find the service by name pattern matching
        $service = self::where('name', 'LIKE', '%' . str_replace('_', ' ', $serviceCode) . '%')
            ->first();
            
        if ($service) {
            return $service->id;
        }
        
        // Fallback mapping
        $serviceIdMap = [
            'rfid_80mm' => 1,   // Update these IDs based on your actual database
            'rfid_120mm' => 2,
            'oil_change' => 3
        ];
        
        return $serviceIdMap[$serviceCode] ?? 1; // Default to 1 if not found
    }
} 