@extends('adminlte::page')

@section('title', 'Search Reports')

@section('content_header')
    <h1>Search Reports</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('search-report') }}" class="form-inline">

                <div class="form-group mr-3">
                    <label for="time_frame" class="mr-2">Time Frame:</label>
                    <select name="time_frame" id="time_frame" class="form-control">
                        <option value="daily" {{ $timeFrame === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $timeFrame === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $timeFrame === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>

                <div class="form-group mr-3">
                    <label for="type" class="mr-2">Type:</label>
                    <select name="type" id="type" class="form-control" onchange="toggleGroupBy()">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All</option>
                        <option value="guest" {{ $type === 'guest' ? 'selected' : '' }}>Guest</option>
                        <option value="user" {{ $type === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>

                <div class="form-group mr-3" id="groupByWrapper" style="{{ in_array($type, ['user', 'all']) ? '' : 'display:none;' }}">
                    <label for="group_by" class="mr-2">Group By:</label>
                    <select name="group_by" id="group_by" class="form-control">
                        <option value="user" {{ $groupBy === 'user' ? 'selected' : '' }}>User</option>
                        <option value="keyword" {{ $groupBy === 'keyword' ? 'selected' : '' }}>Keyword</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="card-body">

            {{-- Guest Reports --}}
            @if(in_array($type, ['guest', 'all']))
                <h3>Guest Search Terms</h3>
                @if($guestReport->isEmpty())
                    <p>No guest search data found for the selected time frame.</p>
                @else
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Number of Searches</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($guestReport as $item)
                                <tr>
                                    <td>{{ $item->term }}</td>
                                    <td>{{ $item->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

            {{-- User Reports --}}
            @if(in_array($type, ['user', 'all']))
                <h3>User Search Terms (Grouped by {{ ucfirst($groupBy) }})</h3>

                @if($userReport->isEmpty())
                    <p>No user search data found for the selected time frame.</p>
                @else
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                @if($groupBy === 'user')
                                    <th>User Name</th>
                                    <th>Keyword</th>
                                    <th>Number of Searches</th>
                                @else
                                    <th>Keyword</th>
                                    <th>Number of Searches</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userReport as $item)
                                <tr>
                                    @if($groupBy === 'user')
                                        <td>{{ $item->user_name }}</td>
                                        <td>{{ $item->term }}</td>
                                        <td>{{ $item->count }}</td>
                                    @else
                                        <td>{{ $item->term }}</td>
                                        <td>{{ $item->count }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

        </div>
    </div>

@stop

@section('js')
<script>
    function toggleGroupBy() {
        const typeSelect = document.getElementById('type');
        const groupByWrapper = document.getElementById('groupByWrapper');
        if (typeSelect.value === 'user' || typeSelect.value === 'all') {
            groupByWrapper.style.display = '';
        } else {
            groupByWrapper.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleGroupBy();
    });
</script>
@stop
