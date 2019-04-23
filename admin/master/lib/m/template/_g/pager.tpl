{:if $Result->PageCount > 1:}
	<style type="text/css">

	</style>

	<ul class="Paging">
		<li><a href="{:$_URL | set_url_param  'p' , 0:}" class="Pagger_First">第一页</a></li>
			{:if $Result->PageCount < 20:}
				{:for var=I from=1 to=$Result->PageCount step=1:}
					{:var J=$I-1:}
				<li>
					{:if $J == $Result->PageIndex:}
						<span class="Pagger_Current">{:$I:}</span>
					{:else:}
						<a href="{:$_URL | set_url_param 'p' , $J:}">{:$I:}</a>
					{:/if:}
				</li>
			{:/for:}
		{:else:}
			{:for var=I from=1 to=10 step=1:}
				{:var J=$I-1:}
				<li>
					{:if $J == $Result->PageIndex:}
						<span class="Pagger_Current">{:$I:}</span>
					{:else:}
						<a href="{:$_URL | set_url_param 'p' , $J:}">{:$I:}</a>
					{:/if:}
				</li>
			{:/for:}
			<li> ... <input type="number" class="input-number" id="__ziima_widget_paging_input" style="text-align:center;"
					   min="1" max="{:echo $Result->PageCount:}" step="1" value="{:echo $Result->PageIndex + 1:}" step="1" /> ...
				<script type="text/javascript">
					document.getElementById('__ziima_widget_paging_input').addEventListener('keyup', function (e) {
						if (e.keyCode === 13 && this.value !== '{:echo $Result->PageIndex + 1:}') {
							this.disabled = true;
							location.href = location.href.setUrlParam('p', parseInt(this.value) - 1);
						}
					}, false);
				</script>
			</li>
			{:for var=I from=$Result->PageCount - 10 to=$Result->PageCount step=1:}
				{:var J=$I-1:}
				<li>
					{:if $J == $Result->PageIndex:}
						<span class="Pagger_Current">{:$I:}</span>
					{:else:}
						<a href="{:$_URL | set_url_param 'p' , $J:}">{:$I:}</a>
					{:/if:}
				</li>
			{:/for:}
		{:/if:}
		{:var T=$Result->PageCount - 1:}
		<li><a href="{:$_URL | set_url_param  'p', $T:}" class="Pagger_Last">最末页</a></li>
	</ul>
{:/if:}
