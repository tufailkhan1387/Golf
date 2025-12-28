@extends('layouts.app')

@section('title', 'Report') <!-- Title specific to this page -->

@section('content')

  
        <div class="content">

            <div class="row">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Summary</h4>
                        <h6>View all Estim 8 detail here</h6>
                    </div>
                    <div class="page-btn">
                        <a href="{{ route('generate_report') }}" class="btn btn-added">Generate Report</a>
                    </div>
                </div>
                <!-- Section 2: Authorization Status -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Total Stock Used</h4><br>
                                <p>Total Lineal Metres: <span id="totalStockUsed" class="fw-bold">{{$totalLength}}</span> mm </p>
                                <p>Total Cost: <span id="totalStockCost" class="fw-bold">${{$totalCost}}</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Total TBC Lengths Cut</h4><br>
                                <p>Total Lineal Metres: <span id="totalTBCLengths" class="fw-bold">0</span> m</p>
                                <p>Total Cost: <span id="totalStockCost" class="fw-bold">$0.00</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Total Waste</h4><br>
                                <p>Total Length: <span id="totalWasteLength" class="fw-bold">{{$wasteLength}}</span> mm</p>
                                {{-- <p>Total Cost: <span id="totalWasteCost" class="fw-bold">$0.00</span></p> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Waste Percentage</h4><br>
                                <p><span id="wastePercentage" class="fw-bold">0</span>%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/home.js') }}"></script>
@endpush
