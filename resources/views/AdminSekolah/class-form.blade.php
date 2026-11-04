{{-- Komponen Livewire: satu elemen root --}}
<div>
    <form wire:submit="simpan" novalidate>
        <div class="tf" style="grid-template-columns:1fr 1fr">
            <section class="tc">
                <h3>Informasi Kelas</h3>

                <div class="fl">
                    <label for="k-nama">Nama Kelas <b>*</b></label>
                    <input id="k-nama" type="text" wire:model="nama" placeholder="Contoh: X MIPA 1" maxlength="255" class="{{ $errors->has('nama') ? 'bad' : '' }}">
                    @error('nama')<small class="er">{{ $message }}</small>@enderror
                </div>

                {{-- Tambahan dari desain: data kelas membutuhkan mata pelajaran --}}
                <div class="fl">
                    <label for="k-mapel">Mata Pelajaran <b>*</b></label>
                    <input id="k-mapel" type="text" wire:model="mata_pelajaran" placeholder="Contoh: Matematika" maxlength="255" class="{{ $errors->has('mata_pelajaran') ? 'bad' : '' }}">
                    @error('mata_pelajaran')<small class="er">{{ $message }}</small>@enderror
                </div>

                <div class="fl">
                    <label for="k-tahun">Tahun Ajaran <b>*</b></label>
                    <select id="k-tahun" wire:model="tahun_ajaran" class="{{ $errors->has('tahun_ajaran') ? 'bad' : '' }}">
                        @foreach ($tahunList as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('tahun_ajaran')<small class="er">{{ $message }}</small>@enderror
                </div>

                <div class="fl">
                    <label for="k-sem">Semester <b>*</b></label>
                    <select id="k-sem" wire:model="semester" class="{{ $errors->has('semester') ? 'bad' : '' }}">
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                    @error('semester')<small class="er">{{ $message }}</small>@enderror
                </div>
            </section>

            <section class="tc">
                <h3>Pengaturan</h3>

                @if ($guruList->isEmpty())
                    <small class="hint" style="margin-top:10px;color:#d93b3b">Belum ada guru aktif di sekolah ini. Tambahkan guru dulu lewat menu Pengguna.</small>
                @endif

                <div class="fl">
                    <label for="k-wali">Wali Kelas <b>*</b></label>
                    <select id="k-wali" wire:model="wali_kelas_id" class="{{ $errors->has('wali_kelas_id') ? 'bad' : '' }}">
                        <option value="">Pilih Guru</option>
                        @foreach ($guruList as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                    @error('wali_kelas_id')<small class="er">{{ $message }}</small>@enderror
                </div>

                <div class="fl">
                    <label for="k-guru">Guru Pengampu <b>*</b></label>
                    <select id="k-guru" wire:model="guru_id" class="{{ $errors->has('guru_id') ? 'bad' : '' }}">
                        <option value="">Pilih Guru</option>
                        @foreach ($guruList as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                    @error('guru_id')<small class="er">{{ $message }}</small>@enderror
                </div>

                <div class="fl">
                    <label for="k-kap">Jumlah Siswa Maksimal <b>*</b></label>
                    <input id="k-kap" type="number" min="1" max="500" wire:model="kapasitas" placeholder="Contoh: 30" class="{{ $errors->has('kapasitas') ? 'bad' : '' }}">
                    @error('kapasitas')<small class="er">{{ $message }}</small>@enderror
                </div>
            </section>
        </div>

        <div class="fa">
            <a href="{{ route('admin.classes.index') }}" class="bt4 gh">Batal</a>
            <button type="submit" class="bt4 pr" wire:loading.attr="disabled">Simpan</button>
        </div>
    </form>
</div>