<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row">

    <div class="col-lg-6">

        <?= form_open('buy', ['class' => 'row g-3']) ?>

        <input type="hidden"
               name="username"
               value="<?= session()->get('username') ?>">

        <input type="hidden"
               name="total_harga_sebelum_diskon"
               value="<?= $total ?>">

        <input type="hidden"
               name="total_harga"
               id="total_harga"
               value="">

        <div class="col-12">
            <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>

            <?= form_input([
                'name'     => 'nama',
                'id'       => 'nama',
                'class'    => 'form-control',
                'value'    => session()->get('username'),
                'readonly' => true
            ]) ?>
        </div>

        <div class="col-12">
            <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>

            <?= form_input([
                'name'  => 'alamat',
                'id'    => 'alamat',
                'class' => 'form-control'
            ]) ?>
        </div>

        <div class="col-12">
            <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>

            <?= form_dropdown(
                'kelurahan',
                [],
                '',
                [
                    'id'    => 'kelurahan',
                    'class' => 'form-control'
                ]
            ) ?>
        </div>

        <div class="col-12">
            <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?>

            <?= form_dropdown(
                'layanan',
                [],
                '',
                [
                    'id'    => 'layanan',
                    'class' => 'form-control'
                ]
            ) ?>
        </div>

        <div class="col-12">
            <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>

            <?= form_input([
                'name'     => 'ongkir',
                'id'       => 'ongkir',
                'class'    => 'form-control',
                'readonly' => true
            ]) ?>
        </div>

        <div class="col-12">
            <?= form_submit(
                'submit',
                'Buat Pesanan',
                ['class' => 'btn btn-primary']
            ) ?>
        </div>

        <?= form_close() ?>

    </div>

    <div class="col-lg-6">

        <table class="table">

            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Sub Total</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($items)) : ?>
                <?php foreach ($items as $item) : ?>

                <tr>
                    <td><?= $item['name'] ?></td>
                    <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                    <td><?= $item['qty'] ?></td>
                    <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                </tr>

                <?php endforeach; ?>
            <?php endif; ?>

            <tr>
                <td colspan="2"></td>
                <td>Subtotal</td>
                <td><?= number_to_currency($total, 'IDR') ?></td>
            </tr>

            <tr>
                <td colspan="2"></td>
                <td style="color:red;">
                    Diskon (<?= $diskon_persen ?>%)
                </td>
                <td style="color:red;">
                    - <?= number_to_currency($diskon_nilai, 'IDR') ?>
                </td>
            </tr>

            <tr>
                <td colspan="2"></td>
                <td>Setelah Diskon</td>
                <td><?= number_to_currency($total_setelah_diskon, 'IDR') ?></td>
            </tr>

            <tr>
                <td colspan="2"></td>
                <td>Ongkir</td>
                <td>
                    <span id="display_ongkir">Rp 0</span>
                </td>
            </tr>
            
            <tr>
                <td colspan="2"></td>
                <td><strong>Total</strong></td>
                <td>
                    <strong>
                        <span id="total">
                            <?= number_to_currency($total_setelah_diskon, 'IDR') ?>
                        </span>
                    </strong>
                </td>
            </tr>

            </tbody>

        </table>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(document).ready(function() {

    let ongkir = 0;
    let subtotal = <?= $total_setelah_diskon ?>;

    hitungTotal();

    function hitungTotal() {

        let grandTotal = subtotal + ongkir;

        $("#ongkir").val(ongkir);
        $("#display_ongkir").text(`IDR ${ongkir.toLocaleString('id-ID')}`);
        $("#total").text(`IDR ${grandTotal.toLocaleString('id-ID')}`);
        $("#total_harga").val(grandTotal);
    }

    $('#kelurahan').select2({
        placeholder: 'Cari daerah tujuan',
        minimumInputLength: 3,
        ajax: {
            url: '<?= site_url('ajax/destinations') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        }
    });

    $("#kelurahan").on('change', function() {

        let id_kelurahan = $(this).val();

        $("#layanan").empty();

        ongkir = 0;

        hitungTotal();

        console.log(id_kelurahan);

        $.ajax({
            url: "<?= site_url('ajax/costs') ?>",
            dataType: "json",
            data: {
                destination: id_kelurahan
            },
            success: function(data) {

                $("#layanan").append(
                    $('<option>', {
                        value: '',
                        text: '-- Pilih Layanan --'
                    })
                );

                data.forEach(function(item) {

                    $("#layanan").append(
                        $('<option>', {
                            value: item.cost,
                            text: `${item.description} (${item.service}) : estimasi ${item.etd}`
                        })
                    );

                });

            }
        });

    });

    $("#layanan").on('change', function() {

        ongkir = parseInt($(this).val()) || 0;

        hitungTotal();

    });

});
</script>
<?= $this->endSection() ?>