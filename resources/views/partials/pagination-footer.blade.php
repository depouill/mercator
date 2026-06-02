<div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2" style="background-color:#fff">
    <small class="text-muted">
        {{ trans('global.showing') }}
        {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
        {{ trans('global.of') }}
        {{ $paginator->total() }}
    </small>
    <div>{{ $paginator->withQueryString()->links() }}</div>
</div>
