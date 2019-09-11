<div class="container-fluid">
    @isset($datatableOptions)
    <div class="row mb-3">
        @isset($datatableOptions['searching'])
        <div class="col-md-4 form-group mb-2 mb-md-0">
            <div class="input-group">
                <label for="datatables-search">Cerca</label>
                <input type="search" class="form-control" id="datatables-search">
                <div class="input-group-append">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-search') }}"></use></svg></div>
                </div>
            </div>
        </div>
        @endisset
        @isset($datatableOptions['columnFilters'])
        <div class="datatable-filters col-md-6 d-flex justify-content-end align-items-center ml-auto">
            <small>{{ __('filtra per:') }}</small>
            @foreach ($datatableOptions['columnFilters'] as $column => $columnFilter)
            <div class="filter dropdown ml-2" data-column-name="{{ $column }}">
                <button class="btn btn-xs btn-outline-primary dropdown-toggle" type="button" id="dropdownFilter-{{ $column }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="filtered">{{ $columnFilter['filterLabel'] }}</span>
                    <span class="not-filtered">{{ $columnFilter['filterLabel'] }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="dropdownFilter-{{ $column }}">
                    <form>
                        <div class="filter-values form-group mb-3"></div>
                    </form>
                    <div class="text-center">
                        <button type="button" class="reset-filters btn btn-xs btn-outline-secondary disabled">{{ __('Azzera filtri') }}</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endisset
    </div>
    @endisset
    <table class="Datatable table responsive{{ ($datatableOptions['textWrap'] ?? false) ? '' : ' nowrap' }}"
        @isset($datatableOptions['serverSide'])
        data-dt-server-side={{ $datatableOptions['serverSide'] ? 'true' : 'false' }}
        @endisset
        @isset($datatableOptions['processing'])
        data-dt-processing={{ $datatableOptions['processing'] ? 'true' : 'false' }}
        @endisset
        @isset($datatableOptions['searching'])
        data-dt-searching={{ $datatableOptions['searching'] ? 'true' : 'false' }}
        @endisset
        data-dt-source="{{ url($source) }}"
        data-dt-columns="{{ json_encode($columns) }}"
        data-dt-columns-order="{{ json_encode($columnsOrder) }}">
        <caption class="sr-only">{{ $caption }}</caption>
        <thead>
            <tr>
                @foreach (Arr::pluck($columns, 'name') as $column)
                    <th scope="col">{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="{{ sizeof($columns) }}">{{ $footer ?? '' }}</th>
            </tr>
        </tfoot>
    </table>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ mix('/css/datatables.css') }}"/>
@endpush
