<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('iblock'))
	return;

$arFilterIBlocks = array(
	array(
		'IBLOCK_TYPE' => 'catalog',
		'IBLOCK_CODE' => 'catalog',
		'IBLOCK_XML_ID' => 'catalog_'.WIZARD_SITE_ID,
	),
	array(
		'IBLOCK_TYPE' => 'catalog',
		'IBLOCK_CODE' => 'offers',
		'IBLOCK_XML_ID' => 'offers_'.WIZARD_SITE_ID,
	),
);

$arrFilterElements = array("offers" => array("...canon_digital_ixus_135" => array("CML2_LINK" => array("catalog" => array("canon_digital_ixus_135",),),),"..zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),".zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"1fujifilm_instax_mini_8s" => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8s",),),),"alcatel_t22" => array("CML2_LINK" => array("catalog" => array("alcatel_t22",),),),"alcatel_t22." => array("CML2_LINK" => array("catalog" => array("alcatel_t22",),),),"apple_iphone_4_16_belyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_4",),),),"apple_iphone_4_16_chyernyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_4",),),),"apple_iphone_4_32_belyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_4",),),),"apple_iphone_4_32_chyernyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_4",),),),"apple_iphone_4_8_belyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_4",),),),"apple_iphone_4_8_chyernyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_4",),),),"apple_iphone_5s_16_belyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_5s",),),),"apple_iphone_5s_16_chernyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_5s",),),),"apple_iphone_5s_32_belyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_5s",),),),"apple_iphone_5s_32_chernyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_5s",),),),"apple_iphone_5s_32_zolotoy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_5s",),),),"apple_iphone_5s_64_belyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_5s",),),),"apple_iphone_5s_64_chyernyy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_5s",),),),"apple_iphone_5s_64_zolotoy" => array("CML2_LINK" => array("catalog" => array("apple_iphone_5s",),),),"bbk_bkt_100_ru" => array("CML2_LINK" => array("catalog" => array("bbk_bkt_100_ru",),),),"bbk_bkt_100_ru." => array("CML2_LINK" => array("catalog" => array("bbk_bkt_100_ru",),),),"bbk_bkt_100_ru.." => array("CML2_LINK" => array("catalog" => array("bbk_bkt_100_ru",),),),"canon_digital_ixus_135" => array("CML2_LINK" => array("catalog" => array("canon_digital_ixus_135",),),),"canon_digital_ixus_135_" => array("CML2_LINK" => array("catalog" => array("canon_digital_ixus_135",),),),"canon_powershot_a3500._is" => array("CML2_LINK" => array("catalog" => array("canon_powershot_a3500_is",),),),"canon_powershot_a3500_is" => array("CML2_LINK" => array("catalog" => array("canon_powershot_a3500_is",),),),"canon_powershot_a3500_is." => array("CML2_LINK" => array("catalog" => array("canon_powershot_a3500_is",),),),"chekhol._knizhka_dlya_samsung_galaxy_s4_lazarr_frame_case_11101165" => array("CML2_LINK" => array("catalog" => array("chekhol_knizhka_dlya_samsung_galaxy_s4_lazarr_frame_case_11101165_",),),),"chekhol._raskladushka_dlya_samsung_galaxy_s4_mini_lazarr_protective_case" => array("CML2_LINK" => array("catalog" => array("chekhol_raskladushka_dlya_samsung_galaxy_s4_mini_lazarr_protective_case",),),),"chekhol_knizhka._dlya_samsung_galaxy_s4_lazarr_frame_case_11101165" => array("CML2_LINK" => array("catalog" => array("chekhol_knizhka_dlya_samsung_galaxy_s4_lazarr_frame_case_11101165_",),),),"chekhol_knizhka_dlya_samsung_galaxy_s4_lazarr_frame_case_11101165" => array("CML2_LINK" => array("catalog" => array("chekhol_knizhka_dlya_samsung_galaxy_s4_lazarr_frame_case_11101165_",),),),"chekhol_raskladushka._dlya_samsung_galaxy_s4_mini_lazarr_protective_case" => array("CML2_LINK" => array("catalog" => array("chekhol_raskladushka_dlya_samsung_galaxy_s4_mini_lazarr_protective_case",),),),"chekhol_raskladushka_dlya_samsung_galaxy_s4_mini_lazarr_protective_case" => array("CML2_LINK" => array("catalog" => array("chekhol_raskladushka_dlya_samsung_galaxy_s4_mini_lazarr_protective_case",),),),"denon_ah_c260" => array("CML2_LINK" => array("catalog" => array("denon_ah_c260",),),),"denon_ah_c260." => array("CML2_LINK" => array("catalog" => array("denon_ah_c260",),),),"fujifilm_instax_.mini_8" => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8",),),),"fujifilm_instax_mini._8" => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8",),),),"fujifilm_instax_mini_.8" => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8",),),),"fujifilm_instax_mini_8" => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8",),),),"fujifilm_instax_mini_8." => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8",),),),"fujifilm_instax_mini_8s" => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8s",),),),"fujifilm_instax_mini_8s." => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8s",),),),"fujifilm_instax_mini_8s1" => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8s",),),),"fujifilm_instax_mini_8s_" => array("CML2_LINK" => array("catalog" => array("fujifilm_instax_mini_8s",),),),"garnitura..._handsfree_dlya_sot_telefona_nokia_wh_920_wh" => array("CML2_LINK" => array("catalog" => array("garnitura_handsfree_dlya_sot_telefona_nokia_wh_920_wh",),),),"garnitura.._handsfree_dlya_sot_telefona_nokia_wh_920_wh" => array("CML2_LINK" => array("catalog" => array("garnitura_handsfree_dlya_sot_telefona_nokia_wh_920_wh",),),),"garnitura._handsfree_dlya_sot_telefona_nokia_wh_920_wh" => array("CML2_LINK" => array("catalog" => array("garnitura_handsfree_dlya_sot_telefona_nokia_wh_920_wh",),),),"garnitura_handsfree.._dlya_sot_telefona_nokia_wh_920_wh" => array("CML2_LINK" => array("catalog" => array("garnitura_handsfree_dlya_sot_telefona_nokia_wh_920_wh",),),),"garnitura_handsfree._dlya_sot_telefona_nokia_wh_920_wh" => array("CML2_LINK" => array("catalog" => array("garnitura_handsfree_dlya_sot_telefona_nokia_wh_920_wh",),),),"garnitura_handsfree_dlya._sot_telefona_nokia_wh_920_wh" => array("CML2_LINK" => array("catalog" => array("garnitura_handsfree_dlya_sot_telefona_nokia_wh_920_wh",),),),"gigaset_da210" => array("CML2_LINK" => array("catalog" => array("gigaset_da210",),),),"gigaset_da210." => array("CML2_LINK" => array("catalog" => array("gigaset_da210",),),),"htc_one_32gb" => array("CML2_LINK" => array("catalog" => array("htc_one_32gb",),),),"htc_one_32gb." => array("CML2_LINK" => array("catalog" => array("htc_one_32gb",),),),"htc_one_32gb.." => array("CML2_LINK" => array("catalog" => array("htc_one_32gb",),),),"htc_one_mini" => array("CML2_LINK" => array("catalog" => array("htc_one_mini",),),),"htc_one_mini." => array("CML2_LINK" => array("catalog" => array("htc_one_mini",),),),"lg_gs_475" => array("CML2_LINK" => array("catalog" => array("lg_gs_475",),),),"lg_gs_475.." => array("CML2_LINK" => array("catalog" => array("lg_gs_475",),),),"lg_gs_4751" => array("CML2_LINK" => array("catalog" => array("lg_gs_475",),),),"lg_gs_480.." => array("CML2_LINK" => array("catalog" => array("lg_gs_480",),),),"lg_gs_480..." => array("CML2_LINK" => array("catalog" => array("lg_gs_480",),),),"lg_nexus_5_16gb_belyy" => array("CML2_LINK" => array("catalog" => array("lg_nexus_5",),),),"lg_nexus_5_16gb_chernyy" => array("CML2_LINK" => array("catalog" => array("lg_nexus_5",),),),"lg_nexus_5_32gb_belyy" => array("CML2_LINK" => array("catalog" => array("lg_nexus_5",),),),"lg_nexus_5_32gb_chernyy" => array("CML2_LINK" => array("catalog" => array("lg_nexus_5",),),),"melkco._i_mee_mono_microusb" => array("CML2_LINK" => array("catalog" => array("melkco_i_mee_mono_microusb",),),),"melkco_i._mee_mono_microusb" => array("CML2_LINK" => array("catalog" => array("melkco_i_mee_mono_microusb",),),),"melkco_i_mee_mono_microusb" => array("CML2_LINK" => array("catalog" => array("melkco_i_mee_mono_microusb",),),),"monster._beats_solo_hd" => array("CML2_LINK" => array("catalog" => array("monster_beats_solo_hd",),),),"monster_beats._solo_hd" => array("CML2_LINK" => array("catalog" => array("monster_beats_solo_hd",),),),"monster_beats_solo._hd" => array("CML2_LINK" => array("catalog" => array("monster_beats_solo_hd",),),),"monster_beats_solo_hd" => array("CML2_LINK" => array("catalog" => array("monster_beats_solo_hd",),),),"monster_beats_solo_hd." => array("CML2_LINK" => array("catalog" => array("monster_beats_solo_hd",),),),"monster_beats_solo_hd.." => array("CML2_LINK" => array("catalog" => array("monster_beats_solo_hd",),),),"monster_urbeats" => array("CML2_LINK" => array("catalog" => array("monster_urbeats",),),),"monster_urbeats." => array("CML2_LINK" => array("catalog" => array("monster_urbeats",),),),"naushnikiakg_k_350" => array("CML2_LINK" => array("catalog" => array("naushnikiakg_k_350",),),),"naushnikiakg_k_350." => array("CML2_LINK" => array("catalog" => array("naushnikiakg_k_350",),),),"naushniki_monster_cable._dna_on_ear" => array("CML2_LINK" => array("catalog" => array("naushniki_monster_cable_dna_on_ear",),),),"naushniki_monster_cable_dna_on_ear" => array("CML2_LINK" => array("catalog" => array("naushniki_monster_cable_dna_on_ear",),),),"naushniki_monster_cable_dna_on_ear." => array("CML2_LINK" => array("catalog" => array("naushniki_monster_cable_dna_on_ear",),),),"nikon._coolpix_s3500" => array("CML2_LINK" => array("catalog" => array("nikon_coolpix_s3500",),),),"nikon_.coolpix_s3500" => array("CML2_LINK" => array("catalog" => array("nikon_coolpix_s3500",),),),"nikon_1_s1_.kit" => array("CML2_LINK" => array("catalog" => array("nikon_1_s1_kit",),),),"nikon_1_s1_kit" => array("CML2_LINK" => array("catalog" => array("nikon_1_s1_kit",),),),"nikon_1_s1_kit." => array("CML2_LINK" => array("catalog" => array("nikon_1_s1_kit",),),),"nikon_coolpix._s3500" => array("CML2_LINK" => array("catalog" => array("nikon_coolpix_s3500",),),),"nikon_coolpix_s.3500" => array("CML2_LINK" => array("catalog" => array("nikon_coolpix_s3500",),),),"nikon_coolpix_s3500" => array("CML2_LINK" => array("catalog" => array("nikon_coolpix_s3500",),),),"nikon_coolpix_s3500." => array("CML2_LINK" => array("catalog" => array("nikon_coolpix_s3500",),),),"panasonic_kx_ft982ru" => array("CML2_LINK" => array("catalog" => array("panasonic_kx_ft982ru",),),),"panasonic_kx_ft982ru/" => array("CML2_LINK" => array("catalog" => array("panasonic_kx_ft982ru",),),),"panasonic_kx_tg2511" => array("CML2_LINK" => array("catalog" => array("panasonic_kx_tg2511",),),),"panasonic_kx_tg2511." => array("CML2_LINK" => array("catalog" => array("panasonic_kx_tg2511",),),),"panasonic_lumix_dmc_gf2_kit" => array("CML2_LINK" => array("catalog" => array("panasonic_lumix_dmc_gf2_kit",),),),"panasonic_lumix_dmc_gf2_kit." => array("CML2_LINK" => array("catalog" => array("panasonic_lumix_dmc_gf2_kit",),),),"philips_d_1501" => array("CML2_LINK" => array("catalog" => array("philips_d_1501",),),),"philips_d_1501." => array("CML2_LINK" => array("catalog" => array("philips_d_1501",),),),"podlinnyy._originalnyy_samsung_s_vid_premium_chekhol_dlya_galaxy_s5_s_v_5_novyy" => array("CML2_LINK" => array("catalog" => array("podlinnyy_originalnyy_samsung_s_vid_premium_chekhol_dlya_galaxy_s5_s_v_5_novyy",),),),"podlinnyy_originalnyy._samsung_s_vid_premium_chekhol_dlya_galaxy_s5_s_v_5_novyy" => array("CML2_LINK" => array("catalog" => array("podlinnyy_originalnyy_samsung_s_vid_premium_chekhol_dlya_galaxy_s5_s_v_5_novyy",),),),"podlinnyy_originalnyy_samsung._s_vid_premium_chekhol_dlya_galaxy_s5_s_v_5_novyy" => array("CML2_LINK" => array("catalog" => array("podlinnyy_originalnyy_samsung_s_vid_premium_chekhol_dlya_galaxy_s5_s_v_5_novyy",),),),"podlinnyy_originalnyy_samsung_s_vid_premium_chekhol_dlya_galaxy_s5_s_v_5_novyy" => array("CML2_LINK" => array("catalog" => array("podlinnyy_originalnyy_samsung_s_vid_premium_chekhol_dlya_galaxy_s5_s_v_5_novyy",),),),"polaroid_300._pic300" => array("CML2_LINK" => array("catalog" => array("polaroid_300_pic300_",),),),"polaroid_300_.pic300" => array("CML2_LINK" => array("catalog" => array("polaroid_300_pic300_",),),),"polaroid_300_pic300" => array("CML2_LINK" => array("catalog" => array("polaroid_300_pic300_",),),),"polaroid_300_pic300." => array("CML2_LINK" => array("catalog" => array("polaroid_300_pic300_",),),),"polaroid_z2300" => array("CML2_LINK" => array("catalog" => array("polaroid_z2300",),),),"polaroid_z2300." => array("CML2_LINK" => array("catalog" => array("polaroid_z2300",),),),"ritmix_rt_500" => array("CML2_LINK" => array("catalog" => array("ritmix_rt_500",),),),"ritmix_rt_500." => array("CML2_LINK" => array("catalog" => array("ritmix_rt_500",),),),"sagemcom_sixty" => array("CML2_LINK" => array("catalog" => array("sagemcom_sixty",),),),"sagemcom_sixty." => array("CML2_LINK" => array("catalog" => array("sagemcom_sixty",),),),"sagemcom_sixty.." => array("CML2_LINK" => array("catalog" => array("sagemcom_sixty",),),),"samsung_galaxy_ace_3" => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_ace_3",),),),"samsung_galaxy_ace_3." => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_ace_3",),),),"samsung_galaxy_core_advance" => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_core_advance",),),),"samsung_galaxy_core_advance1" => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_core_advance",),),),"samsung_galaxy_s4_zoom" => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_s4_zoom",),),),"samsung_galaxy_s4_zoom." => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_s4_zoom",),),),"samsung_galaxy_s5_16gb" => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_s5_16gb",),),),"samsung_galaxy_s5_16gb1" => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_s5_16gb",),),),"samsung_galaxy_s5_16gb2" => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_s5_16gb",),),),"samsung_galaxy_s_ii_plus_gt_i9105" => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_s_ii_plus_gt_i9105",),),),"samsung_galaxy_s_ii_plus_gt_i9105." => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_s_ii_plus_gt_i9105",),),),"samsung_galaxy_trend_gt_s7390" => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_trend_gt_s7390",),),),"samsung_galaxy_trend_gt_s7390." => array("CML2_LINK" => array("catalog" => array("samsung_galaxy_trend_gt_s7390",),),),"setevoe._zaryadnoe_ustroystvo_deppa_ultra_colors_2_usb_data_kabel_30_pin" => array("CML2_LINK" => array("catalog" => array("setevoe_zaryadnoe_ustroystvo_deppa_ultra_colors_2_usb_data_kabel_30_pin",),),),"setevoe_zaryadnoe._ustroystvo_deppa_ultra_colors_2_usb_data_kabel_30_pin" => array("CML2_LINK" => array("catalog" => array("setevoe_zaryadnoe_ustroystvo_deppa_ultra_colors_2_usb_data_kabel_30_pin",),),),"setevoe_zaryadnoe_ustroystvo._deppa_ultra_colors_2_usb_data_kabel_30_pin" => array("CML2_LINK" => array("catalog" => array("setevoe_zaryadnoe_ustroystvo_deppa_ultra_colors_2_usb_data_kabel_30_pin",),),),"setevoe_zaryadnoe_ustroystvo_deppa_ultra._colors_2_usb_data_kabel_30_pin" => array("CML2_LINK" => array("catalog" => array("setevoe_zaryadnoe_ustroystvo_deppa_ultra_colors_2_usb_data_kabel_30_pin",),),),"setevoe_zaryadnoe_ustroystvo_deppa_ultra_colors_2_usb_data_kabel_30_pin" => array("CML2_LINK" => array("catalog" => array("setevoe_zaryadnoe_ustroystvo_deppa_ultra_colors_2_usb_data_kabel_30_pin",),),),"sony._mdr_xb50ap" => array("CML2_LINK" => array("catalog" => array("sony_mdr_xb50ap",),),),"sony_cyber_shot_dsc_.w730" => array("CML2_LINK" => array("catalog" => array("sony_cyber_shot_dsc_w730",),),),"sony_cyber_shot_dsc_w730" => array("CML2_LINK" => array("catalog" => array("sony_cyber_shot_dsc_w730",),),),"sony_cyber_shot_dsc_w730." => array("CML2_LINK" => array("catalog" => array("sony_cyber_shot_dsc_w730",),),),"sony_mdr_xb50ap" => array("CML2_LINK" => array("catalog" => array("sony_mdr_xb50ap",),),),"sony_mdr_xb50ap." => array("CML2_LINK" => array("catalog" => array("sony_mdr_xb50ap",),),),"sony_mdr_xb50ap.." => array("CML2_LINK" => array("catalog" => array("sony_mdr_xb50ap",),),),"sony_xba_c10" => array("CML2_LINK" => array("catalog" => array("sony_xba_c10",),),),"sony_xba_c10." => array("CML2_LINK" => array("catalog" => array("sony_xba_c10",),),),"swissvoice_epure" => array("CML2_LINK" => array("catalog" => array("swissvoice_epure",),),),"swissvoice_epure." => array("CML2_LINK" => array("catalog" => array("swissvoice_epure",),),),"swissvoice_epure.." => array("CML2_LINK" => array("catalog" => array("swissvoice_epure",),),),"texet_tx_226" => array("CML2_LINK" => array("catalog" => array("texet_tx_226",),),),"texet_tx_226." => array("CML2_LINK" => array("catalog" => array("texet_tx_226",),),),"texet_tx_226.." => array("CML2_LINK" => array("catalog" => array("texet_tx_226",),),),"texet_tx_d7955a" => array("CML2_LINK" => array("catalog" => array("texet_tx_d7955a",),),),"texet_tx_d7955a." => array("CML2_LINK" => array("catalog" => array("texet_tx_d7955a",),),),"texet_tx_d7955a.." => array("CML2_LINK" => array("catalog" => array("texet_tx_d7955a",),),),"zhestkiy._chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy._prorezinennye_matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960" => array("CML2_LINK" => array("catalog" => array("zhestkiy_prorezinennye_matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960",),),),"zhestkiy_.chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_.prorezinennye_matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960" => array("CML2_LINK" => array("catalog" => array("zhestkiy_prorezinennye_matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960",),),),"zhestkiy_chekhol._dlya_samsung_galaxy_ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_chekhol_.dlya_samsung_galaxy_ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_chekhol_dlya_samsung._galaxy_ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_chekhol_dlya_samsung_.galaxy_ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_chekhol_dlya_samsung_galaxy._ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_chekhol_dlya_samsung_galaxy_ace._3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_chekhol_dlya_samsung_galaxy_ace_.3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270._s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272" => array("CML2_LINK" => array("catalog" => array("zhestkiy_chekhol_dlya_samsung_galaxy_ace_3_s7270_s7272",),),),"zhestkiy_prorezinennye._matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960" => array("CML2_LINK" => array("catalog" => array("zhestkiy_prorezinennye_matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960",),),),"zhestkiy_prorezinennye_.matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960" => array("CML2_LINK" => array("catalog" => array("zhestkiy_prorezinennye_matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960",),),),"zhestkiy_prorezinennye_matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960" => array("CML2_LINK" => array("catalog" => array("zhestkiy_prorezinennye_matovyy_snap_on_tonkiy_chekhol_dlya_lg_google_nexus_4_e960",),),),),);

$arrFilterElementIDs = array();
$arElementsUsed = array();
$arrIBlockIDs = array();
foreach($arFilterIBlocks as $arFilterIBlock){
	$rsIBlock = CIBlock::GetList(array(), array( 'TYPE' => $arFilterIBlock['IBLOCK_TYPE'], 'CODE' => $arFilterIBlock['IBLOCK_CODE'], 'XML_ID' => $arFilterIBlock['IBLOCK_XML_ID'] ));
	if($arIBlock = $rsIBlock->Fetch()){
		$arrIBlockIDs[$arFilterIBlock['IBLOCK_CODE']] = $arIBlock['ID'];
	}
}
foreach($arrFilterElements as $sCatalogCode1 => $arFilterCatalog1){
	foreach($arFilterCatalog1 as $sElementCode1 => $arFilterElement1){
		$arElementsUsed[$sCatalogCode1][] = $sElementCode1;
		foreach($arFilterElement1 as $sPropertyCode => $arPropertyValue){
			foreach($arPropertyValue as $sCatalogCode2 => $arFilterCatalog2){
				foreach($arFilterCatalog2 as $sElementCode2){
						$arElementsUsed[$sCatalogCode2][] = $sElementCode2;
				}
			}
		}
	}
}
foreach($arElementsUsed as $sCatalogCode => $arCatalogElementsUsed){
	$arElementsUsed[$sCatalogCode] = array_unique($arCatalogElementsUsed);
}
foreach($arElementsUsed as $sCatalogCode => $arCatalogElementsUsed){
$res = CIBlockElement::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $arrIBlockIDs[$sCatalogCode], 'CODE' => $arCatalogElementsUsed));
	while($arElement = $res->GetNext()){
		$arElementIDs[$sCatalogCode][$arElement['CODE']] = $arElement['ID'];
	}
}
foreach($arrFilterElements as $sCatalogCode1 => $arFilterCatalog1){
	foreach($arFilterCatalog1 as $sElementCode1 => $arFilterElement1){
		$arFilterProps = array();
		foreach($arFilterElement1 as $sPropertyCode => $arPropertyValue){
			foreach($arPropertyValue as $sCatalogCode2 => $arFilterCatalog2){
				foreach($arFilterCatalog2 as $sElementCode2){
					$arFilterProps[$sPropertyCode][] = $arElementIDs[$sCatalogCode2][$sElementCode2];

				}
			}
		}
		CIBlockElement::SetPropertyValuesEx($arElementIDs[$sCatalogCode1][$sElementCode1], $arrIBlockIDs[$sCatalogCode1],  $arFilterProps);
	}
}

// ____________________________________________________________________________________________________________________________ //
// ____________________________________________________________________________________________________________________________ //
// ____________________________________________________________________________________________________________________________ //
// ____________________________________________________________________________________________________________________________ //
// ____________________________________________________________________________________________________________________________ //

// add IMAGES

// ____________________________________________________________________________________________________________________________ //

// get elements ID

$arrFilter2 = array(
	'konstruktor_lego_technic_ekskavator_42006' 			=> '1.jpg',
	'crystal_puzzle_piratskiy_korabl_91106' 				=> '2.jpg',
	'konstruktor_lego_city_gruzovoy_vertolyet_4439' 		=> '3.jpg',
	'konstruktor_lego_star_wars_dvorets_dzhabby_9516' 		=> '4.jpg',
	'konstruktor_lego_technic_krossovyy_mototsikl_42007'	=> '5.jpg',
);

$elementIDs = array();
foreach($arrFilter2 as $code => $filePath){
	$resElem = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$arrIBlockIDs['catalog'],'CODE'=>$code), false, Array("nPageSize"=>1), array('ID','CODE','IBLOCK_ID'));
	if($obElem = $resElem->GetNextElement()){
		$arElemFields = $obElem->GetFields();
		$elementIDs[$code] = $arElemFields['ID'];
	}
}

// ____________________________________________________________________________________________________________________________ //

// get sections ID

$arrFilter3 = array(
	'igry_i_igrushki'	=> '1.jpg',
	'odezhda_tekstil'	=> '2.jpg',
	'shkola'			=> '3.jpg',
	'pitanie'			=> '4.jpg',
	'sport_i_otdykh'	=> '5.jpg',
	'malysham'			=> '6.jpg',
	'detskaya_mebel'	=> '7.jpg',
	'kolyaski'			=> '8.jpg',
);

$sectionIDs = array();
foreach($arrFilter3 as $code => $filePath){
	$resSec = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$arrIBlockIDs['catalog'],'CODE'=>$code), false, array('ID','CODE','IBLOCK_ID'), Array("nPageSize"=>1));
	if($arSecFields = $resSec->GetNext()){
		$sectionIDs[$code] = $arSecFields['ID'];
	}
}

// ____________________________________________________________________________________________________________________________ //

// start add preview_picture for elements

if(is_array($elementIDs) && count($elementIDs)>0)
{
	$el = new CIBlockElement;
	
	foreach($arrFilter2 as $code => $filePath){
		$arNewFields = Array(
			'SORT' => 1,
			'PREVIEW_PICTURE' => CFile::MakeFileArray(WIZARD_ABSOLUTE_PATH.'/site/services/iblock/files/elements/'.$filePath),
		);
		$PRODUCT_ID = $elementIDs[$code];
		$res = $el->Update($PRODUCT_ID, $arNewFields);
	}
}

// start add picture for sections

if(is_array($sectionIDs) && count($sectionIDs)>0)
{
	$bs = new CIBlockSection;
	
	foreach($arrFilter3 as $code => $filePath){
		$arNewFields = Array(
			'PICTURE' => CFile::MakeFileArray(WIZARD_ABSOLUTE_PATH.'/site/services/iblock/files/sections/'.$filePath),
		);
		$SECTION_ID = $sectionIDs[$code];
		$res = $bs->Update($SECTION_ID, $arNewFields);
	}
}