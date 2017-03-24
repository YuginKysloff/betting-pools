<? defined('SW_CONSTANT') or die?>

<? if(isset($data['result']['list']) && $data['result']['list']):?>
	
	<? foreach($data['result']['list'] as $val):?>
		<div class="pull">
			<div class="pull__trigger-wrap" data-number="3">
				<span class="pull__trigger">
					<span class="g-currency-usd g-currency-usd--trigger">
						 <?=Lib_Main::beauty_number($val['amount']);?>
					</span>
				</span>
			</div>
			<div class="pull__sitebar">
				<span class="pull__sitebar-row">
					ID
				</span>
				<span class="pull__sitebar-row pull__sitebar-id">
                	#<?=$val['id'];?>
				</span>
				<span class="pull__sitebar-row">
					Текущий процент
				</span>
				<span class="pull__sitebar-row pull__sitebar-procent">
					<?=$val['percent'];?>%
				</span>
				<span class="pull__sitebar-row">
					Сумма в пуле
				</span>
				<span class="g-currency-usd pull__sitebar-row pull__sitebar-sum">
					<?=Lib_Main::beauty_number($val['amount']);?>
				</span>
				
				<? if(isset($val['timer'])):?>
					<span class="pull__sitebar-row">
						Время до закрытия
					</span>
					<span class="pull__sitebar-row pull__sitebar-timeout" id="timer<?=$val['id'];?>">
						<script>
							if(typeof refresh_id<?=$val['id'];?> !=='undefined') clearTimeout(refresh_id<?=$val['id'];?>);
							if(typeof timer_id<?=$val['id'];?> !=='undefined') clearInterval(timer_id<?=$val['id'];?>);
							var hour<?=$val['id'];?> = <?=$val['timer']['hour'];?>;
							var minute<?=$val['id'];?> = <?=$val['timer']['minute'];?>;
							var second<?=$val['id'];?> = <?=$val['timer']['second'];?>;
							function timer<?=$val['id'];?>() {
								$("#timer<?=$val['id'];?>").html((hour<?=$val['id'];?> < 10 ? "0" + hour<?=$val['id'];?> : hour<?=$val['id'];?>) + ":" + (minute<?=$val['id'];?> < 10 ? "0" + minute<?=$val['id'];?> : minute<?=$val['id'];?>) + ":" + (second<?=$val['id'];?> < 10 ? "0" + second<?=$val['id'];?> : second<?=$val['id'];?>));
								--second<?=$val['id'];?>;
								if(second<?=$val['id'];?> < 0) {
									--minute<?=$val['id'];?>;
									if(minute<?=$val['id'];?> < 0) {
										--hour<?=$val['id'];?>;
										if(hour<?=$val['id'];?> < 0) {
											refresh_id<?=$val['id'];?> = setTimeout(function(){
												process('/pools/get_list/p/'+$(".pagination .pagination__button--active").html(), 'list', '#pools__list');
											}, 5000);
										}
										minute<?=$val['id'];?> = 59;
									}
									second<?=$val['id'];?> = 59;
								}
							}
							timer_id<?=$val['id'];?> = setInterval(function(){timer<?=$val['id'];?>()}, 1000);
							timer<?=$val['id'];?>();
						</script>
					</span>
					<form id="pool__form<?=$val['id'];?>" onsubmit="return false;">
						<input type="hidden" name="valute" value="usd">
						<input type="text" name="amount" class="g-input-text g-input-text--pull" data-q placeholder="Сумма" maxlength="9">
					</form>
					
					<? if($data['user']): ?>
						<span class="g-button g-button--pull pools_list__button" data-id="<?=$val['id'];?>">
							Инвестировать
						</span>
					<? else: ?>
						<a class="g-button g-button--pull" href="/login">
							Инвестировать
						</a>
					<? endif?>
					
					<div class="g-red" id="pools__error<?=$val['id']?>">
						
						<? if(isset($data['result']['buyed'][$val['id']])):?>
							<span class="g-success">
								Инвестиция в пул : 
								<span class="g-currency-usd">
									<?=$data['result']['buyed'][$val['id']]?>
								</span>
							</span>
						<? endif?>
						
					</div>
				<? else:?>
					<span class="g-grey">
						Закрыт
					</span>
				<? endif?>
				
				<span class="pull__open-info pools__deposit_info" data-id="<?=$val['id']?>">
					Участники пула
				</span>
			</div>
			<div>
				
				<? foreach($data['config']['marketing']['levels'] as $value):?>
					<div class="pull__item" data-procent="<?=Lib_Main::clear_num(($val['amount'] > ($value['max'] ?? $value['min'])) ? 100 : (($val['amount'] > $value['min']) ? ($val['amount'] * 100 / ($value['max'] ?? $value['min'])) : 0), 0)?>">
						<div class="pull__block pull__block--4"></div>
						<div class="pull__block pull__block--3"></div>
						<div class="pull__block pull__block--2"></div>
						<div class="pull__block pull__block--1"></div>
						<span class="pull__title-procent">
							<?=$value['percent']?>
						</span>
						<div class="pull__title">
							<div>
								<span class="g-grey">от</span>
								<span class="g-currency-usd">
									<?=Lib_Main::beauty_number($value['min'])?>
								</span>
							</div>
							<div>
								
							<? if(!isset($value['max'])):?>
									<span class="g-grey">и более</span>
							<? else:?>
									<span class="g-grey">до</span>
									<span class="g-currency-usd">
										<?=Lib_Main::beauty_number($value['max'])?>
									</span>
							<? endif?>
								
							</div>
						</div>
					</div>
				<? endforeach?>
				
			</div>
			<div class="pull__info" id="pools_list__info<?=$val['id']?>"></div>
		</div>
		<hr class="g-hr">
	<? endforeach?>
	
	<?=$data['result']['pagination'] ?? ''?>
	<script>index.pull();</script>
<? else:?>
	<div class="g-grey">
		Нет результатов
	</div>
<? endif?>