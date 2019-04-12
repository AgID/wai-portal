<table class="Datatable responsive display nowrap u-text-r-xs u-textNoWrap">
    <caption class="u-hiddenVisually">{{ $caption }}</caption>
    <thead>
    <tr>
        @foreach ($columns as $column)
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
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.css"/>
@endpush

@push('scripts')
    <script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
    <script type="text/javascript">
      $(function () {
        $('.Datatable').DataTable({
          ajax: '{{ $source }}',
          responsive: {
            details: {
              type: 'column',
              target: -1
            }
          },
          columns: [
                  @foreach ($columns as $columnName => $column)
            {
              data: '{{ $columnName }}',
                  @if ($columnName == 'actions')
                  render: function (actions) {
                    return actions.map(function (action) {
                        return '<a href="' + action.link + '" role="button" class="Button Button--default Button--shadow Button--round u-padding-top-xxs u-padding-bottom-xxs u-margin-right-s u-text-r-xxs">' + action.label + '</a>'
                    }).join('')
                  }
                  @endif
            },
                  @endforeach
            {
              data: 'control',
              className: 'control',
              orderable: false
            }
          ],
          order: [
                  @foreach ($columnsOrder as $columnOrder)
            [
                  {{ array_search($columnOrder[0], array_keys($columns)) }}, '{{ $columnOrder[1] }}'
            ],
              @endforeach
          ],
          language: {
            url: '//cdn.datatables.net/plug-ins/1.10.16/i18n/Italian.json'
          },
          initComplete: function(settings, json) {
            console.log(settings);
            console.log(json);
            datatablesPostInit(json);
          }
        })
      })
    </script>
@endpush
