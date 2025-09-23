<div class="monitoring-component">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-medium">CPU Usage</h3>
                    <p class="text-3xl font-bold">{{ $cpu ?? 'N/A' }}%</p>
                </div>
            </div>
        </div>
        
        <div class="col-span-12 md:col-span-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-medium">Memory Usage</h3>
                    <p class="text-3xl font-bold">{{ $memory ?? 'N/A' }}%</p>
                </div>
            </div>
        </div>
        
        <div class="col-span-12 md:col-span-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-medium">Disk Usage</h3>
                    <p class="text-3xl font-bold">{{ $disk ?? 'N/A' }}%</p>
                </div>
            </div>
        </div>
    </div>
    
    @if(isset($records) && $records->isNotEmpty())
    <div class="card mt-6">
        <div class="card-header">
            <h3 class="card-title">Historical Data</h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>CPU %</th>
                            <th>Memory %</th>
                            <th>Disk %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                        <tr>
                            <td>{{ $record->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $record->cpu }}</td>
                            <td>{{ $record->memory }}</td>
                            <td>{{ $record->disk }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>