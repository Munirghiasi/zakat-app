@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $user = auth()->user();
    $convertedAmount = $currencyService->convertToUserCurrency($amount, $user);
    $formatted = $currencyService->format($convertedAmount, $user?->currency, $user);
@endphp
{{ $formatted }}

