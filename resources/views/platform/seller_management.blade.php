@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Penjual</h1>
            <p class="text-gray-600 mt-2">Kelola status aktivasi penjual</p>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Seller Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Toko</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">PIC</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Lokasi</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($sellers as $seller)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $seller->nama_toko }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $seller->nama_pic }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $seller->email_pic }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $seller->kabupaten }}, {{ $seller->provinsi }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($seller->status_akun === 'active')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @elseif ($seller->status_akun === 'rejected')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            Nonaktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($seller->status_akun) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('platform.sellers.toggle', ['id' => $seller->id]) }}" method="POST" class="inline">
                                        @csrf
                                        @if ($seller->status_akun === 'active')
                                            <button type="submit" 
                                                class="px-4 py-2 text-sm font-medium bg-red-100 text-red-700 rounded hover:bg-red-200 transition"
                                                onclick="return confirm('Nonaktifkan penjual ini?')">
                                                Nonaktifkan
                                            </button>
                                        @elseif ($seller->status_akun === 'rejected')
                                            <button type="submit"
                                                class="px-4 py-2 text-sm font-medium bg-green-100 text-green-700 rounded hover:bg-green-200 transition">
                                                Aktifkan Kembali
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada penjual untuk dikelola.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($sellers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $sellers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
