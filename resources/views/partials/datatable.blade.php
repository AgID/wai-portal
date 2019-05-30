<table class="Datatable table table-striped responsive nowrap u-text-r-xs u-textNoWrap"
    data-dt-source="{{ json_encode($source) }}"
    data-dt-columns="{{ json_encode($columns) }}"
    data-dt-columns-order="{{ json_encode($columnsOrder) }}">
    <caption class="u-hiddenVisually">{{ $caption }}</caption>
    <thead>
        <tr>
            @foreach (Arr::pluck($columns, 'name') as $column)
                <th scope="col">{{ $column }}</th>
            @endforeach
            <th scope="col"></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th class="u-text-xxs u-textWeight-200 u-textLeft u-textBreak" style="white-space: normal;">{{ $footer ?? '' }}</th>
            @foreach ($columns as $column)
                <th></th>
            @endforeach
        </tr>
    </tfoot>
</table>

@push('styles')
    <link rel="stylesheet" href="{{ mix('/css/datatables.css') }}"/>
@endpush
