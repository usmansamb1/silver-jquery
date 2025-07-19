<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\ServiceBooking;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixMissingReferenceNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-missing-order-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix orders with missing data such as reference numbers and pickup locations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix missing order data...');
        
        // Fix orders with missing reference numbers
        $ordersRefFixed = $this->fixOrderReferenceNumbers();
        $this->info("Fixed {$ordersRefFixed} orders with missing reference numbers.");
        
        // Fix orders with missing pickup locations
        $ordersLocFixed = $this->fixOrderPickupLocations();
        $this->info("Fixed {$ordersLocFixed} orders with missing pickup locations.");
        
        // Fix orders with missing price data
        $ordersPriceFixed = $this->fixOrderPriceData();
        $this->info("Fixed {$ordersPriceFixed} orders with missing price data.");
        
        // Fix orders with missing payment reference
        $ordersPaymentRefFixed = $this->fixOrderPaymentReferences();
        $this->info("Fixed {$ordersPaymentRefFixed} orders with missing payment references.");
        
        // Fix service bookings with missing reference numbers
        $bookingsFixed = $this->fixServiceBookingReferenceNumbers();
        $this->info("Fixed {$bookingsFixed} service bookings with missing reference numbers.");
        
        $this->info('Completed fixing missing order data!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Fix orders with missing reference numbers
     * 
     * @return int Number of fixed orders
     */
    private function fixOrderReferenceNumbers(): int
    {
        $count = 0;
        
        try {
            // Find orders with NULL reference numbers
            $orders = Order::whereNull('reference_number')->get();
            
            foreach ($orders as $order) {
                DB::beginTransaction();
                try {
                    $order->reference_number = 'ORD-' . strtoupper(Str::random(10));
                    $order->save();
                    
                    $count++;
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to fix reference number for order {$order->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Error while fixing order reference numbers: " . $e->getMessage());
        }
        
        return $count;
    }
    
    /**
     * Fix orders with missing pickup locations
     * 
     * @return int Number of fixed orders
     */
    private function fixOrderPickupLocations(): int
    {
        $count = 0;
        
        try {
            // Find orders with NULL pickup_location
            $orders = Order::whereNull('pickup_location')->get();
            
            foreach ($orders as $order) {
                DB::beginTransaction();
                try {
                    // Set a default pickup location
                    $order->pickup_location = 'Default Station';
                    $order->save();
                    
                    $count++;
                    DB::commit();
                    
                    Log::info("Fixed pickup location for order {$order->id}");
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to fix pickup location for order {$order->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Error while fixing order pickup locations: " . $e->getMessage());
        }
        
        return $count;
    }
    
    /**
     * Fix orders with missing price data (subtotal and vat)
     * 
     * @return int Number of fixed orders
     */
    private function fixOrderPriceData(): int
    {
        $count = 0;
        
        try {
            // Find orders with NULL subtotal or vat
            $orders = Order::where(function($query) {
                $query->whereNull('subtotal')->orWhereNull('vat');
            })->get();
            
            foreach ($orders as $order) {
                DB::beginTransaction();
                try {
                    // Calculate from total_amount if available
                    if ($order->total_amount) {
                        // Estimate subtotal and VAT based on VAT rate of 15%
                        $vatRate = 0.15;
                        
                        if ($order->subtotal === null) {
                            $estimatedSubtotal = round($order->total_amount / (1 + $vatRate), 2);
                            $order->subtotal = $estimatedSubtotal;
                        }
                        
                        if ($order->vat === null) {
                            $order->vat = $order->total_amount - $order->subtotal;
                        }
                    } else {
                        // If no total amount, set defaults
                        $order->subtotal = $order->subtotal ?? 0;
                        $order->vat = $order->vat ?? 0;
                        $order->total_amount = $order->total_amount ?? 0;
                    }
                    
                    $order->save();
                    
                    $count++;
                    DB::commit();
                    
                    Log::info("Fixed price data for order {$order->id}");
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to fix price data for order {$order->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Error while fixing order price data: " . $e->getMessage());
        }
        
        return $count;
    }
    
    /**
     * Fix orders with missing payment references
     * 
     * @return int Number of fixed orders
     */
    private function fixOrderPaymentReferences(): int
    {
        $count = 0;
        
        try {
            // Find paid orders with NULL payment_reference
            $orders = Order::where('payment_status', 'paid')
                ->whereNull('payment_reference')
                ->get();
            
            foreach ($orders as $order) {
                DB::beginTransaction();
                try {
                    // Generate a payment reference if none exists
                    if (!$order->payment_reference) {
                        $order->payment_reference = 'PAY-' . strtoupper(Str::random(10));
                        $order->save();
                        
                        $count++;
                        Log::info("Fixed payment reference for order {$order->id}");
                    }
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to fix payment reference for order {$order->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Error while fixing order payment references: " . $e->getMessage());
        }
        
        return $count;
    }
    
    /**
     * Fix service bookings with missing reference numbers
     * 
     * @return int Number of fixed service bookings
     */
    private function fixServiceBookingReferenceNumbers(): int
    {
        $count = 0;
        
        try {
            // Find service bookings with NULL reference numbers
            $bookings = ServiceBooking::whereNull('reference_number')->get();
            
            foreach ($bookings as $booking) {
                DB::beginTransaction();
                try {
                    $booking->reference_number = 'SB-' . strtoupper(uniqid());
                    $booking->save();
                    
                    $count++;
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to fix reference number for booking {$booking->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Error while fixing service booking reference numbers: " . $e->getMessage());
        }
        
        return $count;
    }
}
