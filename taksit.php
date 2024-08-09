<?php
/*
Plugin Name: Woodmart Taksitlendirme Eklentisi
Description: Woodmart teması ile uyumlu taksitlendirme seçeneği sunar.
Version: 1.5
Author: [Omer ATABER JRodix.Com Internet Hizmetleri]
*/

if (!defined('ABSPATH')) {
    exit; 
}


function woodmart_taksitlendirme_eklentisi_aktivasyon() {

}
register_activation_hook(__FILE__, 'woodmart_taksitlendirme_eklentisi_aktivasyon');

function woodmart_taksitlendirme_eklentisi_deaktivasyon() {

}
register_deactivation_hook(__FILE__, 'woodmart_taksitlendirme_eklentisi_deaktivasyon');


function woodmart_taksitlendirme_ayarlar_menusu() {
    add_menu_page(
        'Taksitlendirme Ayarları',
        'Taksitlendirme',
        'manage_options',
        'woodmart-taksitlendirme-ayarlar',
        'woodmart_taksitlendirme_ayarlar_sayfasi'
    );
}
add_action('admin_menu', 'woodmart_taksitlendirme_ayarlar_menusu');

function woodmart_taksitlendirme_ayarlar_sayfasi() {
    ?>
    <div class="wrap">
        <h1>Taksitlendirme Ayarları</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('woodmart_taksitlendirme_ayarlar_grubu');
            do_settings_sections('woodmart-taksitlendirme-ayarlar');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function woodmart_taksitlendirme_ayarlar() {
    register_setting('woodmart_taksitlendirme_ayarlar_grubu', 'woodmart_taksitlendirme_ayarlar');

    add_settings_section(
        'woodmart_taksitlendirme_ayarlar_sekme',
        'Genel Ayarlar',
        null,
        'woodmart-taksitlendirme-ayarlar'
    );

    add_settings_field(
        'woodmart_taksitlendirme_arka_plan_rengi',
        'Arka Plan Rengi',
        'woodmart_taksitlendirme_arka_plan_rengi_input',
        'woodmart-taksitlendirme-ayarlar',
        'woodmart_taksitlendirme_ayarlar_sekme'
    );

    add_settings_field(
        'woodmart_taksitlendirme_yazi_rengi',
        'Yazı Rengi',
        'woodmart_taksitlendirme_yazi_rengi_input',
        'woodmart-taksitlendirme-ayarlar',
        'woodmart_taksitlendirme_ayarlar_sekme'
    );

    add_settings_field(
        'woodmart_taksitlendirme_para_birimi',
        'Para Birimi',
        'woodmart_taksitlendirme_para_birimi_input',
        'woodmart-taksitlendirme-ayarlar',
        'woodmart_taksitlendirme_ayarlar_sekme'
    );
}
add_action('admin_init', 'woodmart_taksitlendirme_ayarlar');

function woodmart_taksitlendirme_arka_plan_rengi_input() {
    $ayarlar = get_option('woodmart_taksitlendirme_ayarlar');
    ?>
    <input type="text" name="woodmart_taksitlendirme_ayarlar[arka_plan_rengi]" value="<?php echo isset($ayarlar['arka_plan_rengi']) ? esc_attr($ayarlar['arka_plan_rengi']) : '#fff3e0'; ?>" class="wp-color-picker-field" data-default-color="#fff3e0" />
    <?php
}

function woodmart_taksitlendirme_yazi_rengi_input() {
    $ayarlar = get_option('woodmart_taksitlendirme_ayarlar');
    ?>
    <input type="text" name="woodmart_taksitlendirme_ayarlar[yazi_rengi]" value="<?php echo isset($ayarlar['yazi_rengi']) ? esc_attr($ayarlar['yazi_rengi']) : '#f9c74f'; ?>" class="wp-color-picker-field" data-default-color="#f9c74f" />
    <?php
}

function woodmart_taksitlendirme_para_birimi_input() {
    $ayarlar = get_option('woodmart_taksitlendirme_ayarlar');
    $para_birimleri = array(
        'TRY' => '₺',
        'USD' => '$',
        'EUR' => '€',
        'MNT' => '₼',
    );
    ?>
    <select name="woodmart_taksitlendirme_ayarlar[para_birimi]">
        <?php foreach ($para_birimleri as $kod => $ad) : ?>
            <option value="<?php echo esc_attr($kod); ?>" <?php selected($ayarlar['para_birimi'], $kod); ?>>
                <?php echo esc_html($ad); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

function woodmart_taksitlendirme_urun_detayi() {
    global $product;

    $taksit_ekle = get_post_meta($product->get_id(), '_taksit_ekle', true);
    $taksit_sayisi = get_post_meta($product->get_id(), '_taksit_sayisi', true);

    $ayarlar = get_option('woodmart_taksitlendirme_ayarlar');
    $arka_plan_rengi = isset($ayarlar['arka_plan_rengi']) ? esc_attr($ayarlar['arka_plan_rengi']) : '#fff3e0';
    $yazi_rengi = isset($ayarlar['yazi_rengi']) ? esc_attr($ayarlar['yazi_rengi']) : '#f9c74f';
    $para_birimi = isset($ayarlar['para_birimi']) ? esc_attr($ayarlar['para_birimi']) : 'TRY';

    $para_birimleri = array(
        'TRY' => '₺',
        'USD' => '$',
        'EUR' => '€',
        'MNT' => '₼',
    );

    $para_birimi_simge = isset($para_birimleri[$para_birimi]) ? $para_birimleri[$para_birimi] : '';

    if ($taksit_ekle === 'yes' && $taksit_sayisi > 0) {
        $taksitli_fiyat = $product->get_price() / $taksit_sayisi;
        ?>
        <p class="taksitlendirme-mesaj" style="background-color: <?php echo $arka_plan_rengi; ?>; color: <?php echo $yazi_rengi; ?>; display: inline-block; padding: 10px; border-radius: 5px; max-width: 200px; text-align: center; font-weight: bold;">
            <strong>Taksit</strong>
            <span class="taksitlendirme-fiyat">
                <?php echo number_format($taksitli_fiyat, 2, '.', ','); ?>
            </span> <span class="woocommerce-Price-currencySymbol"><?php echo $para_birimi_simge; ?></span> x <?php echo esc_html($taksit_sayisi); ?> ay
        </p>
        <?php
    }
}
add_action('woocommerce_single_product_summary', 'woodmart_taksitlendirme_urun_detayi', 20);

function woodmart_taksitlendirme_listede_urunler() {
    global $product;

    $taksit_ekle = get_post_meta($product->get_id(), '_taksit_ekle', true);
    $taksit_sayisi = get_post_meta($product->get_id(), '_taksit_sayisi', true);

    $ayarlar = get_option('woodmart_taksitlendirme_ayarlar');
    $arka_plan_rengi = isset($ayarlar['arka_plan_rengi']) ? esc_attr($ayarlar['arka_plan_rengi']) : '#fff3e0';
    $yazi_rengi = isset($ayarlar['yazi_rengi']) ? esc_attr($ayarlar['yazi_rengi']) : '#f9c74f';
    $para_birimi = isset($ayarlar['para_birimi']) ? esc_attr($ayarlar['para_birimi']) : 'TRY';

    $para_birimleri = array(
        'TRY' => '₺',
        'USD' => '$',
        'EUR' => '€',
        'MNT' => '₼',
    );

    $para_birimi_simge = isset($para_birimleri[$para_birimi]) ? $para_birimleri[$para_birimi] : '';

    if ($taksit_ekle === 'yes' && $taksit_sayisi > 0) {
        $taksitli_fiyat = $product->get_price() / $taksit_sayisi;
        ?>
        <p class="taksitlendirme-listede" style="background-color: <?php echo $arka_plan_rengi; ?>; color: <?php echo $yazi_rengi; ?>; display: inline-block; padding: 5px; border-radius: 5px; max-width: 150px; text-align: center; font-weight: bold;">
            <strong>Taksit</strong>
            <span class="taksitlendirme-fiyat">
                <?php echo number_format($taksitli_fiyat, 2, '.', ','); ?>
            </span> <span class="woocommerce-Price-currencySymbol"><?php echo $para_birimi_simge; ?></span> x <?php echo esc_html($taksit_sayisi); ?> ay
        </p>
        <?php
    }
}
add_action('woocommerce_after_shop_loop_item_title', 'woodmart_taksitlendirme_listede_urunler', 20);
function woodmart_taksitlendirme_urun_düzenleme_ekle() {
    global $post;
    if ($post->post_type === 'product') {
        $taksit_ekle = get_post_meta($post->ID, '_taksit_ekle', true);
        $taksit_sayisi = get_post_meta($post->ID, '_taksit_sayisi', true);
        ?>
        <div class="options_group">
            <p class="form-field">
                <label for="_taksit_ekle">Taksitlendirme Aktif</label>
                <select id="_taksit_ekle" name="_taksit_ekle">
                    <option value="yes" <?php selected($taksit_ekle, 'yes'); ?>>Evet</option>
                    <option value="no" <?php selected($taksit_ekle, 'no'); ?>>Hayır</option>
                </select>
            </p>
            <p class="form-field">
                <label for="_taksit_sayisi">Taksit Sayısı</label>
                <input type="number" id="_taksit_sayisi" name="_taksit_sayisi" value="<?php echo esc_attr($taksit_sayisi); ?>" />
            </p>
        </div>
        <?php
    }
}
add_action('woocommerce_product_options_general_product_data', 'woodmart_taksitlendirme_urun_düzenleme_ekle');
function woodmart_taksitlendirme_urun_düzenleme_kaydet($post_id) {
    if (isset($_POST['_taksit_ekle'])) {
        update_post_meta($post_id, '_taksit_ekle', sanitize_text_field($_POST['_taksit_ekle']));
    }
    if (isset($_POST['_taksit_sayisi'])) {
        update_post_meta($post_id, '_taksit_sayisi', sanitize_text_field($_POST['_taksit_sayisi']));
    }
}
add_action('woocommerce_process_product_meta', 'woodmart_taksitlendirme_urun_düzenleme_kaydet');
?>
