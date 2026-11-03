<x-filament-panels::page>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        @foreach($cards as $card)
            <a href="{{ $card['url'] }}" style="display: block; height: 100%;">
                <x-filament::section style="height: 100%;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="padding: 0.75rem; border-radius: 0.5rem; background-color: rgba(var(--{{ $card['color'] }}-500), 0.1); color: rgb(var(--{{ $card['color'] }}-500));">
                            <x-icon name="{{ $card['icon'] }}" style="width: 1.5rem; height: 1.5rem;" />
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: 600;">
                            {{ $card['title'] }}
                        </h3>
                    </div>
                    <p style="color: gray; font-size: 0.875rem;">
                        {{ $card['description'] }}
                    </p>
                </x-filament::section>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
