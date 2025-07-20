{{-- This partial view displays map location information with proper Arabic text support --}}
<div class="map-location-item">
    <div class="location-header">
        <h4 dir="auto" lang="ar">{{ $location->name }}</h4>
        <span class="badge bg-{{ $location->status === 'verified' ? 'success' : 'warning' }}">
            {{ ucfirst($location->status) }}
        </span>
    </div>
    
    <div class="location-details">
        @if($location->city)
            <div class="detail-item">
                <span class="detail-label">City:</span>
                <span class="detail-value" dir="auto" lang="ar">{{ $location->city }}</span>
            </div>
        @endif
        
        @if($location->region)
            <div class="detail-item">
                <span class="detail-label">Region:</span>
                <span class="detail-value">{{ $location->region }}</span>
            </div>
        @endif
        
        <div class="detail-item">
            <span class="detail-label">Coordinates:</span>
            <span class="detail-value">{{ $location->latitude }}, {{ $location->longitude }}</span>
        </div>
        
        @if($location->address)
            <div class="detail-item">
                <span class="detail-label">Address:</span>
                <span class="detail-value" dir="auto" lang="ar">{{ $location->address }}</span>
            </div>
        @endif
    </div>
    
    <div class="location-actions">
        <a href="{{ $location->google_maps_url }}" target="_blank" class="btn btn-sm btn-primary">
            <i class="fas fa-map-marker-alt"></i> View Map
        </a>
    </div>
</div>

<style>
    /* Add support for RTL and Arabic text */
    [dir="auto"] {
        unicode-bidi: embed;
    }
    
    [lang="ar"] {
        font-family: 'Segoe UI', Tahoma, sans-serif;
    }
    
    .map-location-item {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .location-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .location-header h4 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }
    
    .detail-item {
        margin-bottom: 5px;
    }
    
    .detail-label {
        font-weight: 600;
        margin-right: 10px;
    }
    
    .location-actions {
        margin-top: 10px;
    }
</style>
