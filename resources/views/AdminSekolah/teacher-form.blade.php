{{-- Komponen Livewire: satu elemen root --}}
<div>
<style>
.tf{display:grid;grid-template-columns:1.1fr 1fr;gap:16px;align-items:start}
.tc{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 2px 8px rgba(20,35,63,.05);padding:20px 22px}
.tc h3{margin:0 0 6px;font-size:13px;font-weight:700}
.fl{margin-top:14px}
.fl label,.fl .lbl{display:block;font-size:11.5px;color:#41506b;margin-bottom:6px}
.fl label b{color:#e5484d;font-weight:600}
.fl input[type=text],.fl input[type=email],.fl input[type=tel],.fl select{width:100%;height:40px;border:1px solid #d6e2f4;border-radius:8px;background:#fff;padding:0 12px;font:inherit;font-size:12px;color:var(--ink)}
.fl select{appearance:auto}
.fl input::placeholder{color:#a3b2c8}
.fl input:focus-visible,.fl select:focus-visible{outline:2px solid #2f6fe4;border-color:#2f6fe4}
.fl .bad{border-color:#e5484d}
.er{display:block;color:#d93b3b;font-size:11px;margin-top:4px}
.hint{display:block;color:var(--muted);font-size:11px;margin-top:5px}
.rd{display:flex;gap:26px;margin-top:4px}
.rd label{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ink);cursor:pointer;margin:0}
.rd input{accent-color:#1f6bff;width:16px;height:16px;margin:0}
.fa{display:flex;justify-content:flex-end;gap:12px;margin-top:18px}
.bt4{font:inherit;font-size:12.5px;font-weight:600;border-radius:9px;padding:10px 30px;cursor:pointer;text-decoration:none;display:inline-block;border:1px solid transparent}
.bt4.gh{background:#fff;color:#2a3956;border-color:#d6e2f4}
.bt4.pr{background:#1f6bff;color:#fff;box-shadow:0 3px 8px rgba(31,107,255,.3)}
.bt4:disabled{opacity:.6;cursor:wait}
.bt4:focus-visible{outline:2px solid #1f6bff;outline-offset:2px}
@media (max-width:900px){.tf{grid-template-columns:1fr}}
</style>

    <form wire:submit="simpan" novalidate>
        <div class="tf">
            <section class="tc">
                <h3>Data Pribadi</h3>

                <div class="fl">
                    <label for="g-name">Nama Lengkap <b>*</b></label>
                    <input id="g-name" type="text" wire:model="name" placeholder="Masukkan nama lengkap" maxlength="255" class="{{ $errors->has('name') ? 'bad' : '' }}">
                    @error('name')<small class="er">{{ $message }}</small>@enderror
                </div>

                <div class="fl">
                    <label for="g-nip">NIP <b>*</b></label>
                    <input id="g-nip" type="text" wire:model="nomor_induk" placeholder="Masukkan NIP" maxlength="30" class="{{ $errors->has('nomor_induk') ? 'bad' : '' }}">
                    @error('nomor_induk')<small class="er">{{ $message }}</small>@enderror
                </div>

                <div class="fl">
                    <label for="g-email">Email <b>*</b></label>
                    <input id="g-email" type="email" wire:model="email" placeholder="contoh@email.com" maxlength="255" class="{{ $errors->has('email') ? 'bad' : '' }}">
                    @error('email')<small class="er">{{ $message }}</small>@enderror
                </div>

                <div class="fl">
                    <label for="g-hp">No. Telepon</label>
                    <input id="g-hp" type="tel" wire:model="no_telepon" placeholder="08xxxxxxxxxx" maxlength="20" class="{{ $errors->has('no_telepon') ? 'bad' : '' }}">
                    @error('no_telepon')<small class="er">{{ $message }}</small>@enderror
                </div>
            </section>

            <section class="tc">
                <h3>Data Mengajar</h3>

                @if ($mapelList->isEmpty())
                    <small class="hint" style="margin-top:10px">Belum ada kelas di sekolah ini, jadi guru bisa disimpan dulu dan ditugaskan ke kelas nanti.</small>
                @endif

                <div class="fl">
                    <label for="g-mapel">Mata Pelajaran @if ($mapelList->isNotEmpty())<b>*</b>@endif</label>
                    <select id="g-mapel" wire:model.live="mapel" {{ $mapelList->isEmpty() ? 'disabled' : '' }} class="{{ $errors->has('mapel') ? 'bad' : '' }}">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach ($mapelList as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                    @error('mapel')<small class="er">{{ $message }}</small>@enderror
                </div>

                <div class="fl">
                    <label for="g-kelas">Kelas Pengampu @if ($mapelList->isNotEmpty())<b>*</b>@endif</label>
                    <select id="g-kelas" wire:model="class_id" {{ $mapelList->isEmpty() || $mapel === '' ? 'disabled' : '' }} class="{{ $errors->has('class_id') ? 'bad' : '' }}">
                        <option value="">Pilih Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}{{ $k->guru ? ' (saat ini: '.$k->guru.')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('class_id')<small class="er">{{ $message }}</small>@enderror
                    <small class="hint">Kalau kelas sudah punya guru, guru tersebut akan digantikan.</small>
                </div>

                <div class="fl">
                    <span class="lbl">Status</span>
                    <div class="rd" role="radiogroup" aria-label="Status">
                        <label><input type="radio" wire:model="status" value="aktif"> Aktif</label>
                        <label><input type="radio" wire:model="status" value="nonaktif"> Nonaktif</label>
                    </div>
                </div>
            </section>
        </div>

        <div class="fa">
            <a href="{{ route('admin.teachers.index') }}" class="bt4 gh">Batal</a>
            <button type="submit" class="bt4 pr" wire:loading.attr="disabled">Simpan</button>
        </div>
    </form>
</div>