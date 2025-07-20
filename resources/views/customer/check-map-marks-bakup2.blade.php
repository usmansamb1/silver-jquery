@extends('layouts.app')

@section('title', 'Find Nearest Station - JOIL YASEEIR')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <div class="col-12">
            <x-map-viewer 
                :fullPage="true"
                :showNearestButton="true"
                :controls="true"
            />
            
            <!-- Nearest Location Toast -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="nearestLocationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <strong class="me-auto">Location Found</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body" id="nearest-toast-message"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.map-full-container {
    position: relative;
    height: calc(100vh - 70px);
    width: 100%;
}

#map-container {
    height: 100%;
    width: 100%;
}

#google-my-map {
    height: 100%;
    width: 100%;
    border: none;
}

.map-controls-container {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    display: flex;
    flex-direction: column;
    align-items: center;
}

#location-status {
    min-width: 300px;
    text-align: center;
}

.toast-container {
    z-index: 1100;
}
</style>
@endpush 