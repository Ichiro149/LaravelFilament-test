<div class="space-y-4">
    <div>
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Request Payload</h3>
        <pre class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg text-xs overflow-auto max-h-64"><code>{{ $payload }}</code></pre>
    </div>

    @if($response)
    <div>
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Response Body</h3>
        <pre class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg text-xs overflow-auto max-h-64"><code>{{ $response }}</code></pre>
    </div>
    @endif
</div>
