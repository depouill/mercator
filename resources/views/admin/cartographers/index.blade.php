@extends('layouts.admin')

@section('title')
    {{ trans('cruds.cartographer.title') }}
@endsection

@section('content')
<div style="margin-bottom: 10px;" class="row">
    @can('cartographer_create')
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route('admin.cartographers.create') }}">
            {{ trans('global.add') }} {{ trans('cruds.cartographer.title_singular') }}
        </a>
    </div>
    @endcan
</div>

<div class="card">
    <div class="card-header">
        {{ trans('cruds.cartographer.title') }}
    </div>
    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.cartographer.fields.type') }}</th>
                        <th>{{ trans('cruds.cartographer.fields.object') }}</th>
                        <th>{{ trans('cruds.cartographer.fields.user') }}</th>
                        <th>{{ trans('cruds.cartographer.fields.role') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartographers as $cartographer)
                    @php
                        $showRoute = $routes[$cartographer->cartographiable_type] ?? null;
                    @endphp
                    <tr>
                        <td></td>

                        {{-- Type --}}
                        <td>{{ $models[$cartographer->cartographiable_type] ?? $cartographer->cartographiable_type }}</td>

                        {{-- Objet (lien vers la fiche) --}}
                        <td>
                            @if($cartographer->cartographiable)
                                @php($label = $cartographer->cartographiable->name ?? '(id:'.$cartographer->cartographiable_id.')')
                                @if($showRoute)
                                    <a href="{{ route($showRoute, $cartographer->cartographiable_id) }}">{{ $label }}</a>
                                @else
                                    {{ $label }}
                                @endif
                            @else
                                <em class="text-muted">(objet supprimé)</em>
                            @endif
                        </td>

                        {{-- Utilisateur --}}
                        <td>
                            @if($cartographer->user)
                                @can('user_show')
                                    <a href="{{ route('admin.users.show', $cartographer->user_id) }}">{{ $cartographer->user->name }}</a>
                                @else
                                    {{ $cartographer->user->name }}
                                @endcan
                            @else
                                -
                            @endif
                        </td>

                        {{-- Rôle --}}
                        <td>
                            @if($cartographer->role)
                                @can('role_show')
                                    <a href="{{ route('admin.roles.show', $cartographer->role_id) }}">{{ $cartographer->role->title }}</a>
                                @else
                                    {{ $cartographer->role->title }}
                                @endcan
                            @else
                                -
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td nowrap>
                            @can('cartographer_edit')
                            <a class="btn btn-xs btn-info"
                               href="{{ route('admin.cartographers.edit', $cartographer->id) }}">
                                {{ trans('global.edit') }}
                            </a>
                            @endcan
                            @can('cartographer_delete')
                            <form action="{{ route('admin.cartographers.destroy', $cartographer->id) }}" method="POST"
                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                  style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="submit" class="btn btn-xs btn-danger"
                                       value="{{ trans('global.delete') }}">
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $cartographers->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
@include('partials.datatable', [
    'id'                  => '#dataTable',
    'title'               => trans('cruds.cartographer.title_singular'),
    'URL'                 => '',
    'canDelete'           => auth()->user()->can('cartographer_delete') ? true : false,
    'serverSidePagination' => false,
])
</script>
@endsection
