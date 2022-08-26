{**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<section id="cointopay_order_confirmation_section" style="width:80%;display:table;margin:10% auto 0 auto;padding:6% 20px;background-color: #f1f1f1;text-align: center;">
    <h3 class="h3 card-title">Options de carte de credit / Credit card options / Kreditkartenoptionen
     <br>Attention au frais de cartes +2.50€ - Mind the card fees +2.50€ - Achten Sie auf Kartengebühren +2.50€ </br></h3>
        <h3 class="h3 card-title">Option 1: &quot;EU CreditCard (EUR Only)&quot;=BUNQ<br>
  Option 2:
&quot;International CreditCard (EUR Only)&quot;=WYRE + APPLE PAY</h3>
<h3 class="h3 card-title"><br>
  <br>
  Utilisez OPTION 1 en premier, si cela ne fonctionne pas utilisez OPTION 2 .
    <br>
Verwenden Sie zuerst OPTION 1, wenn dies nicht funktioniert, verwenden Sie OPTION 2.<br>
Use OPTION 1 first, if it doesn't work use OPTION 2.</h3>

    <div class="cointopay-sofort-login-content">
 <p>{$smarty.get.PaymentDetailCConly|unescape: "html" nofilter}</p>
           
            
</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<form method="post" action="/module/cointopay_direct_sofort/callback" id="CoinsPaymentCallBack">
<input type="hidden" name="CustomerReferenceNr" id="CustomerReferenceNr" value="{$smarty.get.CustomerReferenceNr|escape:'htmlall':'UTF-8'}" />
<input type="hidden" name="ConfirmCode" id="ConfirmCode" value="{$smarty.get.ConfirmCode|escape:'htmlall':'UTF-8'}" />
<input type="hidden" name="status" id="CoinsPaymentStatus" value="" />
<input type="hidden" name="notenough" id="CoinsPaymentnotenough" value="" />
<input type="hidden" name="COINTOPAY_DIRECT_SOFORT_MERCHANT_ID" id="COINTOPAY_DIRECT_SOFORT_MERCHANT_ID" value="{$smarty.get.merchant_id|escape:'htmlall':'UTF-8'}" />
<input type="hidden" name="TransactionID" id="COINTOPAY_DIRECT_SOFORT_TransactionID" value="{$smarty.get.TransactionID|escape:'htmlall':'UTF-8'}" />
<input type="hidden" name="CoinAddressUsed" id="CoinAddressUsed" value="{$smarty.get.coinAddress|escape:'htmlall':'UTF-8'}" />
<input type="hidden" name="SecurityCode" id="SecurityCode" value="{$smarty.get.SecurityCode|escape:'htmlall':'UTF-8'}" />
<input type="hidden" name="AltCoinID" id="AltCoinID" value="{$smarty.get.AltCoinID|escape:'htmlall':'UTF-8'}" />
<input type="hidden" name="RedirectURL" id="RedirectURL" value="{$smarty.get.RedirectURL|escape:'htmlall':'UTF-8'}" />
</form>
<script type="text/javascript">
jQuery(document).ready(function ($) {
 jQuery('#cointopay-sofort-modal-6-0').modal('show');
jQuery('.inline_popup_cointopay').click(function(){
jQuery('#cointopay-sofort-modal-6-0').modal('show');
});
$('html, body').animate({
        scrollTop: $('#cointopay_order_confirmation_section').offset().top
    }, 'slow')
});
       
jQuery(document).ready(function ($) {
	
var d1 = new Date (),
                            d2 = new Date ( d1 );
                            d2.setMinutes ( d1.getMinutes() + {$smarty.get.ExpiryTime|escape:'htmlall':'UTF-8'} );
                            var countDownDate = d2.getTime();
                            // Update the count down every 1 second
                            var x = setInterval(function() {
                                if ($('#expire_time').length) {
                                    // Get todays date and time
                                    var now = new Date().getTime();
                                    
                                    // Find the distance between now an the count down date
                                    var distance = countDownDate - now;
                                    
                                    // Time calculations for days, hours, minutes and seconds
                                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                    
                                    // Output the result in an element with id="expire_time"
                                    document.getElementById("expire_time").innerHTML = days + "d " + hours + "h "
                                    + minutes + "m " + seconds + "s ";
                                    
                                    // If the count down is over, write some text 
                                    if (distance < 0) {
                                        clearInterval(x);
                                        document.getElementById("expire_time").innerHTML = "EXPIRED";
                                    }
                                }
                            }, 1000);


                        
});                           
    </script>

