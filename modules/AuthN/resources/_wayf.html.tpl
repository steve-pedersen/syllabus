<div class="container">
	<form method="get" action="login{if $returnTo}?returnTo={$returnTo}{/if}" class="prominent data">
	    <div class="field input-append">
	        <label class="field-label field-linked" for="login-wayf">Select your Identity Provider: </label>
	        <input type="hidden" name="returnTo" value="{$returnTo}">
	        <select name="idp" id="login-wayf appendedInputButtons" class="span4">
				{foreach key="providerId" item="providerName" from=$providerList}
	            <option value="{$providerId|escape}"{if $smarty.cookies.wayfSettings && $smarty.cookies.wayfSettings == $providerId} selected="selected"{/if}>		{$providerName|escape}
	            </option>
				{/foreach}
	        </select>
	        
	        <button class="btn btn-primary" type="submit" name="command[choose]">Login</button>
	    </div>
	</form>
</div>