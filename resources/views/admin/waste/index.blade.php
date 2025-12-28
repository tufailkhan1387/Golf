@extends('layouts.app')

@section('title', 'Stock Managment') <!-- Title specific to this page -->

@section('content')

    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Waste Management</h4>
                    <h6>View all waste here</h6>
                </div>
            </div>

        </div>
        <div class="card table-list-card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-input">
                            <a href="javascript:void(0);" class="btn btn-searchset"><i data-feather="search"
                                    class="feather-search"></i></a>
                        </div>
                    </div>


                </div>

                <div class="table-responsive product-list">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th class="no-sort">
                                    <label class="checkboxs">
                                        <input type="checkbox" id="select-all">
                                        <span class="checkmarks"></span>
                                    </label>
                                </th>
                                <th>Waste Length (mm)</th>
                                <th>Quantity</th>

                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wastes as $stock)
                                <tr>
                                    <td>
                                        <label class="checkboxs">
                                            <input type="checkbox">
                                            <span class="checkmarks"></span>
                                        </label>
                                    </td>
                                    <td>{{ $stock->length }}</td>
                                    <td>{{ $stock->qty }}</td>
                                    <td class="action-table-data">
                                        <div class="edit-delete-action">


                                            <a href="{{route('delete_waste',['id'=>$stock->id])}}" class=" p-2">
                                                <i data-feather="trash-2" class="feather-trash-2"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    </div>






    <script>
        function edit(stock) {
            $('#edit-stock input[name="id"]').val(stock.id);
            $('#edit-stock input[name="edit_length"]').val(stock.length);
            $('#edit-stock input[name="edit_cost"]').val(stock.cost);
            $('#edit-stock').modal('show');
        }
    </script>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/home.js') }}"></script>
@endpush
