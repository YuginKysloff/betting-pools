<? defined('SW_CONSTANT') or die?> 

<footer class="footer">
    <span class="footer__copyright">
        Copyright to <?=$data['config']['site']['name'];?>
    </span>
    <span class="footer__mail">
        <a class="footer__mail-link" href="mailto:asdasd@asds.sd">
            <?=$data['config']['site']['support'];?>
        </a>
    </span>
    <span class="footer__skype">
        <a class="footer__skype-link" href="skype:login?chat">
            Betting-pools
        </a>
    </span>
    <span class="footer__paysystem">
        
        <?foreach($data['config']['payments'] as $key => $val):
                if(!$val['fill'] && !$val['payout']) continue?>
                <a class="footer__link" href="#">
                    <img class="footer__img" src="/views/<?=$template;?>/img/<?=$key;?>.png" alt="">
                </a>
        <?endforeach?>
        
    </span>
</footer>