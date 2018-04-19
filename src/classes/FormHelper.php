<?php
/**
 * Module is prohibited to sales! Violation of this condition leads to the deprivation of the license!
 *
 * @author   Yandex.Money <cms@yamoney.ru>
 * @copyright © 2015-2017 NBCO Yandex.Money LLC
 * @license   https://money.yandex.ru/doc.xml?id=527052
 *
 * @category Front Office Features
 * @package  Yandex Payment Solution
 */

namespace YandexMoneyModule;

use Carrier;
use Configuration;
use Context;
use Module;
use OrderState;
use Tax;
use Tools;
use YandexCheckout\Model\PaymentMethodType;
use yandexmodule;

class FormHelper
{
    public $cats;

    private $module;

    public function l($s)
    {
        if ($this->module === null) {
            $this->module = Module::getInstanceByName('yandexmodule');
        }
        return $this->module->l($s, 'FormHelper');
    }

    public function getMarketOrdersForm()
    {
        $module = new yandexmodule();
        $dir = _PS_ADMIN_DIR_;
        $dir = explode('/', $dir);
        $dir = base64_encode(
            $module->getCipher()->encrypt(
                end($dir).'_'.Context::getContext()->cookie->id_employee.'_market_orders'
            )
        );
        $carriers = Carrier::getCarriers(Context::getContext()->language->id, true, false, false, null, 5);
        $type = array(
            array(
                'name' => 'POST',
                'id' => 'POST'
            ),
            array(
                'name' => 'PICKUP',
                'id' => 'PICKUP'
            ),
            array(
                'name' => 'DELIVERY',
                'id' => 'DELIVERY'
            )
        );
        $out = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Module configuration Orders on the market'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('A token for access to API Yandex.Market'),
                        'name' => 'YA_MARKET_ORDERS_TOKEN',
                        'label' => $this->l('An authorization token Yandex.Market'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'disabled' => 1,
                        'type' => 'text',
                        'name' => 'YA_MARKET_ORDERS_FD',
                        'label' => $this->l('Data format'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'disabled' => 1,
                        'type' => 'text',
                        'name' => 'YA_MARKET_ORDERS_TA',
                        'label' => $this->l('Тип авторизации'),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Prepayment'),
                        'name' => 'YA_MARKET_ORDERS_PREDOPLATA',
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'YANDEX',
                                    'name' => $this->l('Payment at registration (only in Russia)'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'SHOP_PREPAID',
                                    'name' => $this->l('Directly to the shop (only for Ukraine)'),
                                    'val' => 1
                                ),

                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Post-paid'),
                        'name' => 'YA_MARKET_ORDERS_POSTOPLATA',
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'CASH_ON_DELIVERY',
                                    'name' => $this->l('Cash upon receipt of goods'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'CARD_ON_DELIVERY',
                                    'name' => $this->l('Payment via credit card upon receipt of order'),
                                    'val' => 1
                                ),

                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Settings'),
                        'name' => 'YA_MARKET_ORDERS_SET',
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'CHANGEC',
                                    'name' => $this->l('To enable the change of delivery'),
                                    'val' => 1
                                ),

                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                    /*array(
                        'col' => 4,
                        'class' => 't disabled',
                        'type' => 'text',
                        //'desc' => $this->l('Ссылка на https://api.partner.market.yandex.ru/v2/'),
                        'name' => 'YA_MARKET_ORDERS_APIURL',
                        'label' => $this->l('URL affiliate API Yandex.Market'),
                        'value' => 'https://api.partner.market.yandex.ru/v2/',
                    ),*/
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('The Campaign Number'),
                        'name' => 'YA_MARKET_ORDERS_NC',
                        'label' => $this->l('The Campaign Number'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('The user login in the system Yandex.Market'),
                        'name' => 'YA_MARKET_ORDERS_LOGIN',
                        'label' => $this->l('The user login in the system Yandex.Market'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        //'desc' => $this->l('Application ID'),
                        'name' => 'YA_MARKET_ORDERS_ID',
                        'label' => $this->l('Application ID'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        //'desc' => $this->l('Password prilozheniye'),
                        'name' => 'YA_MARKET_ORDERS_PW',
                        'label' => $this->l('An application-specific password'),
                    ),array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => '<a href="https://oauth.yandex.ru/authorize?response_type=code&display=popup&state='
                            .$dir.'&client_id='.Configuration::get('YA_MARKET_ORDERS_ID')."&device_id="
                            .md5(Configuration::get('YA_MARKET_ORDERS_ID')).'">'
                            .$this->l('To obtain a token for access to Yandex.Buy').'</a>',
                        'name' => 'YA_MARKET_ORDERS_YATOKEN',
                        'label' => $this->l('An authorization token'),
                        'disabled' => true
                    ),
                    array(
                        'col' => 6,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('Item number ex'),
                        'name' => 'YA_MARKET_ORDERS_PUNKT',
                        'label' => $this->l('The ID of the item ex'),
                    ),
                    array(
                        'col' => 6,
                        'class' => 't',
                        'type' => 'text',
                        'name' => 'YA_MARKET_REDIRECT',
                        'desc' => $this->l('Callback Url for OAuth applications'),
                        'label' => $this->l('The link for the application'),
                    ),
                    array(
                        'col' => 6,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('URL API to fill in the store settings on Yandex.Market'),
                        'name' => 'YA_MARKET_ORDERS_APISHOP',
                        'label' => $this->l('The link to access Your store'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        foreach ($carriers as $a) {
            $out['form']['input'][] = array(
                        'type' => 'select',
                        'label' => $this->l('The delivery type').' '.$a['name'],
                        'name' => 'YA_MARKET_ORDERS_DELIVERY_'.$a['id_carrier'],
                        'desc' =>$this->l('POST - Mail DELIVERY - Express delivery, PICKUP Pickup'),
                        'options' => array(
                            'query' => $type,
                            'name' => 'name',
                            'id' => 'id'
                        ),
                        'class' => 't'
                    );
        }

        return $out;
    }

    public function getFormYamoneyMarket()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('The module settings Yandex.Market'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Simplified yml:'),
                        'name' => 'YA_MARKET_SHORT',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Included')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Off')
                            )
                        ),
                    ),
                    array(
                        'type' => 'categories',
                        'label' => $this->l('Categories'),
                        'desc' => $this->l('Select the categories to export. If you need a subcategory, select them.'),
                        'name' => 'YA_MARKET_CATEGORIES',
                        'tree' => array(
                            'use_search' => false,
                            'id' => 'categoryBox',
                            'use_checkbox' => true,
                            'selected_categories' => $this->cats,
                        ),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('To unload:'),
                        'name' => 'YA_MARKET_CATALL',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('All categories')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Only selected')
                            )
                        ),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('The name of your company for Yandex.Market'),
                        'name' => 'YA_MARKET_NAME',
                        'label' => $this->l('The name of the store'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('The shipping cost to your home location'),
                        'name' => 'YA_MARKET_DELIVERY',
                        'label' => $this->l('The shipping cost to your home location'),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Type paged descriptions'),
                        'name' => 'YA_MARKET_DESC_TYPE',
                        'class' => 't',
                        'values' => array(
                            array(
                                'id' => 'NORMAL',
                                'value' => 0,
                                'label' => $this->l('Full')
                            ),
                            array(
                                'id' => 'SHORT',
                                'value' => 1,
                                'label' => $this->l('Short')
                            )
                        ),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Availability'),
                        'desc' => $this->l('Availability'),
                        'name' => 'YA_MARKET_DOSTUPNOST',
                        'is_bool' => false,
                        'values' => array(
                            array(
                                'id' => 'd_0',
                                'value' => 0,
                                'label' => $this->l('All available')
                            ),
                            array(
                                'id' => 'd_1',
                                'value' => 1,
                                'label' => $this->l('If available > 0, the rest to order')
                            ),
                            array(
                                'id' => 'd_2',
                                'value' => 2,
                                'label' => $this->l('If = 0, do not unload')
                            ),
                            array(
                                'id' => 'd_3',
                                'value' => 3,
                                'label' => $this->l('All made to order')
                            )
                        )
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Settings'),
                        'name' => 'YA_MARKET_SET',
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'AVAILABLE',
                                    'name' => $this->l('To export only the goods which are in stock'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'NACTIVECAT',
                                    'name' => $this->l('To exclude inactive categories'),
                                    'val' => 1
                                ),
                                /*array(
                                    'id' => 'HOMECARRIER',
                                    'name' => $this->l('To use the delivery at your home location'),
                                    'val' => 1
                                ),*/
                                array(
                                    'id' => 'COMBINATIONS',
                                    'name' => $this->l('Export of product combinations'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'DIMENSIONS',
                                    'val' => 1,
                                    'name' => $this->l('Display dimensions of product (dimensions)')
                                ),
                                array(
                                    'id' => 'ALLCURRENCY',
                                    'val' => 1,
                                    'name' =>
                                        $this->l('Unload all currencies? (If not, will be uploaded only by default)')
                                ),
                                array(
                                    'id' => 'GZIP',
                                    'val' => 1,
                                    'name' => $this->l('Gzip compression')
                                ),
                                array(
                                    'id' => 'ROZNICA',
                                    'val' => 1,
                                    'name' => $this->l('the opportunity to buy in a retail store.')
                                ),
                                array(
                                    'id' => 'DOST',
                                    'val' => 1,
                                    'name' => $this->l(' the possibility of delivery of the product.')
                                ),
                                array(
                                    'id' => 'SAMOVIVOZ',
                                    'val' => 1,
                                    'name' => $this->l('the ability to reserve and pick up yourself.')
                                ),

                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'col' => 6,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('Link to the dynamic file price list'),
                        'name' => 'YA_MARKET_YML',
                        'label' => $this->l('The yml file'),
                    ),
                    array(
                        'col' => 6,
                        'class' => 't',
                        'type' => 'text',
                        'name' => 'YA_MARKET_REDIRECT',
                        'label' => $this->l('The redirect link for the application.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    'generatemanual' => array(
                        'title' => $this->l('To generate manually'),
                        'name' => 'generatemanual',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh'
                    )
                ),
            ),
        );
    }

    public function getFormYandexMetrics()
    {
        $module = new yandexmodule();
        $dir = _PS_ADMIN_DIR_;
        $dir = explode('/', $dir);
        $dir = base64_encode(
            $module->getCipher()->encrypt(
                end($dir).'_'.Context::getContext()->cookie->id_employee.'_metrika'
            )
        );
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('The module settings Yandex.The metric'),
                'icon' => 'icon-cogs',
                ),
            'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Activity:'),
                        'name' => 'YA_METRICS_ACTIVE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('The number of Your counter'),
                        'name' => 'YA_METRICS_NUMBER',
                        'label' => $this->l('The number of the counter'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('ID of the OAuth application'),
                        'name' => 'YA_METRICS_ID_APPLICATION',
                        'label' => $this->l('Application ID'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l('The password of the OAuth application'),
                        'name' => 'YA_METRICS_PASSWORD_APPLICATION',
                        'label' => $this->l('An application-specific password'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => '<a href="https://oauth.yandex.ru/authorize?response_type=code&display=popup&state='.
                            $dir.'&client_id='.Configuration::get('YA_METRICS_ID_APPLICATION').'">'
                            .$this->l('To request a token for accessing the Yandex.The metric').'</a>',
                        'name' => 'YA_METRICS_TOKEN',
                        'label' => $this->l('The OAuth Token'),
                        'disabled' => true
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Settings'),
                        'name' => 'YA_METRICS_SET',
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'WEBVIZOR',
                                    'name' => $this->l('Vebvizor'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'CLICKMAP',
                                    'name' => $this->l('Map clicks'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'OUTLINK',
                                    'name' => $this->l('External links, file downloads and report the "Share"button'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'OTKAZI',
                                    'name' => $this->l('Accurate bounce rate'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'HASH',
                                    'name' => $this->l('Hash tracking in the browser address bar'),
                                    'val' => 1
                                ),

                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('To collect statistics on the following circuits:'),
                        'name' => 'YA_METRICS_CELI',
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'CART',
                                    'name' => $this->l('Basket(Visitor has clicked "add to cart")'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'ORDER',
                                    'name' => $this->l('Ordering(Visitor checkout)'),
                                    'val' => 1
                                ),
                                array(
                                    'id' => 'WISHLIST',
                                    'name' => $this->l('Wishlist(Visitor added an item to wishlist)'),
                                    'val' => 1
                                )
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'col' => 6,
                        'class' => 't',
                        'type' => 'text',
                        'name' => 'YA_METRICS_REDIRECT',
                        'desc' => $this->l('Callback Url for OAuth applications'),
                        'label' => $this->l('The link for the application'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    public function getKassaForm(Models\KassaModel $model)
    {
        $paymentMethodOptions = array(
            'query' => array(),
            'id'    => 'id',
            'name'  => 'name',
        );
        $names = array(
            PaymentMethodType::BANK_CARD => 'Банковские карты',
            PaymentMethodType::YANDEX_MONEY => 'Яндекс.Деньги',
            PaymentMethodType::SBERBANK => 'Сбербанк Онлайн',
            PaymentMethodType::QIWI => 'QIWI Wallet',
            PaymentMethodType::WEBMONEY => 'Webmoney',
            PaymentMethodType::CASH => 'Наличные через терминалы',
            PaymentMethodType::MOBILE_BALANCE => 'Баланс мобильного',
            PaymentMethodType::ALFABANK => 'Альфа-Клик',
        );
        foreach (array_keys($model->getPaymentMethods()) as $key) {
            $paymentMethodOptions['query'][] = array(
                'id' => Tools::strtoupper($key),
                'name' => $this->l($names[$key]),
                'val' => 1,
            );
        }

        $form =  array(
            'form' => array(
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l(
                            'Включить приём платежей через Яндекс.Кассу'
                        ),
                        'name' => 'YA_KASSA_ACTIVE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'value' => ($model->isEnabled() ? 1 : 0),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l(
                            'Скопируйте shopId из личного кабинета Яндекс.Кассы'
                        ),
                        'name' => 'YA_KASSA_SHOP_ID',
                        'required' => true,
                        'label' => $this->l('shopId'),
                        'value' => $model->getShopId(),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'required' => true,
                        'desc' => 'Выпустите и активируйте секретный ключ в '
                            . '<a href="https://kassa.yandex.ru/my" target="_blank">'
                            . 'личном кабинете Яндекс.Кассы</a>. '
                            . 'Потом скопируйте его сюда.'
                        ,
                        'name' => 'YA_KASSA_PASSWORD',
                        'label' => $this->l('Секретный ключ'),
                        'value' => $model->getPassword(),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Выбор способа оплаты'),
                        'desc' => '',
                        'name' => 'YA_KASSA_PAYMENT_MODE',
                        'required' => false,
                        'class' => 't',
                        'value' => $model->getEPL() ? 'kassa' : 'shop',
                        'values' => array(
                            array(
                                'id' => 'payment_mode_kassa',
                                'value' => 'kassa',
                                'label' => $this->l('На стороне Кассы'),
                            ),
                            array(
                                'id' => 'payment_mode_shop',
                                'value' => 'shop',
                                'label' => $this->l('На стороне магазина'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => '',
                        'name' => 'YA_KASSA_PAY_LOGO',
                        'class' => 'text-inside payment-mode-kassa',
                        'desc' => $this->l(''),
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'ON',
                                    'name' => $this->l('Use "Pay with Yandex" button'),
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => '',
                        'desc' => '',
                        'name' => 'YA_KASSA_PAYMENT',
                        'class' => 'payment-mode-shop',
                        'values' => $paymentMethodOptions,
                    ),

                    array(
                        'type' => 'radio',
                        'label' => $this->l('Send receipt to Yandex.Kassa (54 federal law)'),
                        'name' => 'YA_KASSA_SEND_RECEIPT',
                        'desc' => $this->l(''),
                        'value' => ($model->getSendReceipt() ? 1 : 0),
                        'values' => array(
                            array(
                                'id' => 'kassa_send_receipt_enable',
                                'label' => $this->l('Включить'),
                                'value' => 1,
                            ),
                            array(
                                'id' => 'kassa_send_receipt_disable',
                                'label' => $this->l('Отключить'),
                                'value' => 0,
                            ),
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    'downloadlog' => array(
                        'title' => $this->l('Download log file'),
                        'name' => 'downloadlog',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-refresh'
                    )
                ),
            )
        );

        $taxRateOptions = array(
            'query' => array(
                array(
                    'id' => 1,
                    'name' => 'Без НДС',
                ),
                array(
                    'id' => 2,
                    'name' => '0%',
                ),
                array(
                    'id' => 3,
                    'name' => '10%',
                ),
                array(
                    'id' => 4,
                    'name' => '18%',
                ),
                array(
                    'id' => 5,
                    'name' => 'Расчётная ставка 10/110',
                ),
                array(
                    'id' => 6,
                    'name' => 'Расчётная ставка 18/118',
                )
            ),
            'id' => 'id',
            'name' => 'name',
        );

        $form['form']['input'][] = array(
            'type'  => 'html',
            'label' => $this->l('НДС'),
            'html_content' => '',
            'desc' => '',
            'name' => '',
        );

        $form['form']['input'][] = array(
            'type'  => 'select',
            'label' => $this->l('Ставка по умолчанию'),
            'name'  => 'YA_KASSA_DEFAULT_TAX_RATE',
            'options' => $taxRateOptions,
            'value' => $model->getDefaultTaxRate(),
            'html_content' => '',
            'desc' => $this->l('Default tax rate'),
        );

        $form['form']['input'][] = array(
            'type'  => 'html',
            'label' => $this->l('Сопоставьте ставки'),
            'html_content' => '',
            'desc' => $this->l(''),
            'name' => '',
            'class' => 'kassa_tax_rate',
        );

        $form['form']['input'][] = array(
            'type'  => 'html',
            'label' => $this->l('Ставка в вашем магазине'),
            'html_content' => '',
            'desc' => $this->l('Ставка для чека в налоговую.'),
            'name' => '',
            'class' => 'kassa_tax_rate',
        );

        foreach (Tax::getTaxes(Context::getContext()->language->id, true) as $tax) {
            $form['form']['input'][] = array(
                'type' => 'select',
                'label' => $tax['name'],
                'name' => 'YA_KASSA_TAX_RATE_' . $tax['id_tax'],
                'class' => 'kassa_tax_rate',
                'options' => $taxRateOptions,
            );
        }

        $statusList = OrderState::getOrderStates(Context::getContext()->language->id);
        $statusOptions = array(
            'query' => $statusList,
            'id' => 'id_order_state',
            'name' => 'name',
        );

        $form['form']['input'][] = array(
            'col' => 6,
            'class' => 't',
            'type' => 'text',
            'desc' => $this->l(
                'Этот адрес понадобится, если его '
                . 'попросят специалисты Яндекс.Кассы'
            ),
            'name' => 'YA_KASSA_NOTIFICATION_URL',
            'label' => $this->l('Адрес для уведомлений'),
            'disabled' => true
        );

        /*$form['form']['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Статус заказа после создания'),
            'name' => 'YA_KASSA_CREATE_STATUS_ID',
            'value' => $model->getCreateStatusId(),
            'options' => $statusOptions,
        );*/
        $form['form']['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Статус заказа после подтверждения платежа'),
            'name' => 'YA_KASSA_SUCCESS_STATUS_ID',
            'value' => $model->getSuccessStatusId(),
            'options' => $statusOptions,
        );
        /*$form['form']['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Статус заказа после неудачного платежа'),
            'name' => 'YA_KASSA_FAILURE_STATUS_ID',
            'value' => $model->getFailureStatusId(),
            'options' => $statusOptions,
        );*/


        $form['form']['input'][] = array(
            'col' => 4,
            'class' => 't',
            'type' => 'text',
            'desc' => $this->l(''),
            'name' => 'YA_KASSA_MIN',
            'value' => $model->getMinimumAmount(),
            'label' => $this->l('Минимальная сумма заказа'),
        );
        $form['form']['input'][] = array(
            'type' => 'checkbox',
            'label' => $this->l('Запись отладочной информации'),
            'name' => 'YA_KASSA_LOGGING',
            'desc' => $this->l(
                'Настройку нужно будет поменять, '
                . 'если попросят специалисты Яндекс.Кассы'
            ),
            'values' => array(
                'query' => array(
                    array(
                        'id' => 'ON',
                        'name' => ''
                    ),
                ),
                'id' => 'id',
                'name' => 'name'
            ),
        );
        return $form;
    }

    public function getWalletForm(Models\WalletModel $model)
    {
        return array(
            'form' => array(
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l(
                            'Включить прием платежей в кошелек на Яндексе'
                        ),
                        'name' => 'YA_WALLET_ACTIVE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'value' => $model->isEnabled() ? '1' : '0',
                    ),
                    array(
                        'col' => 6,
                        'class' => 't',
                        'desc' => "Скопируйте эту ссылку в поле Redirect URL на ".
                            "<a href=\"https://sp-money.yandex.ru/myservices/new.xml\" target=\"_blank\">"
                            .$this->l("странице регистрации приложения")."</a>",
                        'type' => 'text',
                        'name' => 'YA_WALLET_REDIRECT',
                        'label' => $this->l('RedirectURL'),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l(''),
                        'name' => 'YA_WALLET_ACCOUNT_ID',
                        'label' => $this->l('Номер кошелька'),
                        'value' => $model->getAccountId(),
                    ),
                    array(
                        'col' => 6,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l(''),
                        'name' => 'YA_WALLET_APPLICATION_ID',
                        'label' => $this->l('Id приложения'),
                        'value' => $model->getApplicationId(),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Секретное слово'),
                        'name' => 'YA_WALLET_PASSWORD',
                        'rows' => 5,
                        'cols' => 30,
                        'desc' => $this->l(''),
                        'class' => 't',
                        'value' => $model->getPassword(),
                    ),
                    array(
                        'col' => 9,
                        'class' => 't',
                        'type' => 'free',
                        'name' => 'YA_WALLET_TEXT_INSIDE',
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l(''),
                        'name' => 'YA_WALLET_MIN_AMOUNT',
                        'label' => $this->l('Minimum order amount'),
                        'value' => $model->getMinimumAmount()
                    ),
                    array(
                        'type' => 'checkbox',
                        'label' => $this->l('Запись отладочной информации'),
                        'desc' => $this->l('Настройку нужно будет поменять, ".
                            "только если попросят специалисты Яндекс.Денег'),
                        'name' => 'YA_WALLET_LOGGING',
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => 'ON',
                                    'name' => '',
                                    'val' => 1
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),
                ),
            'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    public function getBillingForm(Models\BillingModel $model)
    {
        $state = new OrderState();
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('The module settings Yandex.Billing'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Activate payments via Yandex.Billing'),
                        'name' => 'YA_BILLING_ACTIVE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'value' => ($model->isEnabled() ? '1' : '0')
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'label' => $this->l('Yandex.Billing\'s identifier'),
                        'name' => 'YA_BILLING_ID',
                        'value' => $model->getFormId(),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'text',
                        'desc' => $this->l(
                            'Payment purpose is added to the payment order: specify whatever will help identify the'
                            . ' order paid via Yandex.Billing'
                        ),
                        'name' => 'YA_BILLING_PURPOSE',
                        'label' => $this->l('Payment purpose'),
                        'default' => $this->l('Order No. #order_id# Payment via Yandex.Billing'),
                        'value' => $model->getPurpose(),
                    ),
                    array(
                        'col' => 4,
                        'class' => 't',
                        'type' => 'select',
                        'desc' => $this->l(
                            'Order status shows the payment result is unknown: you can only learn whether the client'
                            . ' made payment or not from an email notification or in your bank'
                        ),
                        'name' => 'YA_BILLING_END_STATUS',
                        'label' => $this->l('Order status'),
                        'options' => array(
                            'query' => $state->getOrderStates(1),
                            'id' => 'id_order_state',
                            'name' => 'name'
                        ),
                        'default' => Configuration::get('PS_OS_PAYMENT'),
                        'value' => $model->getOrderStatus(),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }
}
