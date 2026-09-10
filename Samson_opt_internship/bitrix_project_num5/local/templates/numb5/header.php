<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<html lang="<?=LANGUAGE_ID?>">
    <head>
        <title><?$APPLICATION->ShowTitle()?></title>
        <?
        $APPLICATION->ShowHead();
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/template_style.css');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery-1.8.2.min.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/slides.min.jquery.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.carouFredSel-6.1.0-packed.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.carouFredSel-6.1.0-packed.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/functions.js');
        ?>


        <!--[if gte IE 9]><style type="text/css">.gradient {filter: none;}</style><![endif]-->
    </head>
<body>
<?$APPLICATION->ShowPanel()?>
    <div class="wrap">
        <div class="hd_header_area">
            <div class="hd_header">
                <table>
                    <tr>
                        <td rowspan="2" class="hd_companyname">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                Array(
                                    "AREA_FILE_SHOW" => "file",
                                    "AREA_FILE_SUFFIX" => "",
                                    "EDIT_TEMPLATE" => "",
                                    "PATH" => "/local/include/name.php"
                                )
                            );?>

                        </td>
                        <td rowspan="2" class="hd_txarea">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                Array(
                                    "AREA_FILE_SHOW" => "file",
                                    "AREA_FILE_SUFFIX" => "",
                                    "EDIT_TEMPLATE" => "",
                                    "PATH" => "/local/include/telef.php"
                                )
                            );?>
                            <br/>
                            время работы <span class="workhours">ежедневно с 9-00 до 18-00</span>
                        </td>
                        <td style="width:232px">
                            <form action="">
                                <div class="hd_search_form" style="float:right;">
                                    <input placeholder="Поиск" type="text"/>
                                    <input type="submit" value=""/>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 11px;">
                                <span class="hd_singin"><a id="hd_singin_but_open" href="">Войти на сайт</a>
                                <div class="hd_loginform">
                                    <span class="hd_title_loginform">Войти на сайт</span>
                                    <form name="" method="" action="">

                                        <input placeholder="Логин"  type="text">
                                        <input  placeholder="Пароль"  type="password">
                                        <a href="/" class="hd_forgotpassword">Забыли пароль</a>

                                        <div class="head_remember_me" style="margin-top: 10px">
                                            <input id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" type="checkbox">
                                            <label for="USER_REMEMBER_frm" title="Запомнить меня на этом компьютере">Запомнить меня</label>
                                        </div>
                                        <input value="Войти" name="Login" style="margin-top: 20px;" type="submit">
                                        </form>
                                    <span class="hd_close_loginform">Закрыть</span>
                                </div>
                                </span><br>
                            <a href="" class="hd_signup">Зарегистрироваться</a>
                        </td>
                    </tr>
                </table>

                <div class="nv_topnav">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "top",
                        Array(
                            "ALLOW_MULTI_SELECT" => "N",
                            "CHILD_MENU_TYPE" => "left",
                            "DELAY" => "N",
                            "MAX_LEVEL" => "3",
                            "MENU_CACHE_GET_VARS" => array(""),
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "ROOT_MENU_TYPE" => "top",
                            "USE_EXT" => "Y"
                        )
                    );?>
                </div>

            </div>
        </div>

        <!--- // end header area --->
        <div class="bc_breadcrumbs">
            <ul>
            <?$APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "nav",
                Array(
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "0"
                )
            );?>
            </ul>
            <div class="clearboth"></div>
        </div>

        <div class="main_container page">
            <div class="mn_container">
                <div class="mn_content">
                    <div class="main_post">
                        <div class="main_title">
                            <p class="title"><?$APPLICATION->ShowTitle('h1')?></p>
                        </div>
