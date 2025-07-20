@extends('layouts.app')

@section('title', __('Your Saved Cards'))

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">{{ __('Your Saved Cards') }}</h3>
                </div>
                <div class="card-body">
                    @if($cards->count() > 0)
                        <div class="row">
                            @foreach($cards as $card)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border {{ $card->is_default ? 'border-primary' : 'border-secondary' }}">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                @switch(strtolower($card->card_brand))
                                                    @case('visa')
                                                        <i class="fab fa-cc-visa fa-2x text-primary me-2"></i>
                                                        @break
                                                    @case('mastercard')
                                                        <i class="fab fa-cc-mastercard fa-2x text-danger me-2"></i>
                                                        @break
                                                    @case('amex')
                                                        <i class="fab fa-cc-amex fa-2x text-info me-2"></i>
                                                        @break
                                                    @case('discover')
                                                        <i class="fab fa-cc-discover fa-2x text-warning me-2"></i>
                                                        @break
                                                    @default
                                                        <i class="fa fa-credit-card fa-2x text-secondary me-2"></i>
                                                @endswitch
                                                <h5 class="card-title mb-0">{{ ucfirst($card->card_brand) }}</h5>
                                            </div>
                                            
                                            <h6 class="card-subtitle mb-2 text-muted">{{ $card->masked_number }}</h6>
                                            <p class="card-text">{{ __('Expires:') }} {{ $card->formatted_expiry }}</p>
                                            
                                            @if($card->is_default)
                                                <span class="badge bg-primary">{{ __('Default') }}</span>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent d-flex justify-content-between">
                                            @if(!$card->is_default)
                                                <form action="{{ route('services.cards.set-default', $card) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        {{ __('Set as Default') }}
                                                    </button>
                                                </form>
                                            @else
                                                <span></span>
                                            @endif
                                            
                                            <form action="{{ route('services.cards.delete', $card) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure you want to remove this card?') }}')">
                                                    {{ __('Remove') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('You don\'t have any saved cards yet. Cards will be saved when you make a purchase and select the "Save this card for future purchases" option.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle any card deletions with confirmation
        $('.btn-outline-danger').on('click', function(e) {
            if (!confirm('{{ __('Are you sure you want to remove this card?') }}')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush 