@include('partials.head')
@include('partials.navbar')

<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16">
            <!-- Form -->
            <div>
                <h1 class="text-4xl font-bold text-slate-900 mb-6">Hubungi Kami</h1>
                <p class="text-slate-600 mb-8 text-lg">Punya pertanyaan tentang sistem atau mengalami kendala teknis? Tim support kami siap membantu.</p>
                
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition" placeholder="john@sekolah.sch.id">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Pesan</label>
                        <textarea rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition" placeholder="Tulis pesan Anda..."></textarea>
                    </div>
                    <button type="submit" class="w-full md:w-auto px-8 py-3.5 bg-slate-900 text-white font-semibold hover:bg-slate-800 transition rounded-sm">
                        Kirim Pesan
                    </button>
                </form>
            </div>

            <!-- Info -->
            <div class="bg-slate-50 p-10 border border-slate-200 rounded-lg h-fit">
                <h3 class="text-xl font-bold text-slate-900 mb-8">Informasi Kontak</h3>
                
                <div class="space-y-8">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-teal-600 shrink-0 mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Alamat</h4>
                            <p class="text-slate-600 mt-1">Jl. Pendidikan No. 123, Bantul, Yogyakarta</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-teal-600 shrink-0 mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Email</h4>
                            <p class="text-slate-600 mt-1">info@apic.sch.id</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-teal-600 shrink-0 mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Jam Operasional</h4>
                            <p class="text-slate-600 mt-1">Senin - Jumat: 08:00 - 16:00 WIB</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')
@include('partials.scripts')