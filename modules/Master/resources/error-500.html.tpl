<h1>Internal Server Error <span class="minor">(HTTP 500)</span></h1>
<p>
    Something has gone wrong, but we're not sure just what, yet.
</p>
{if $xxx}
<h2>Debugging information</h2>
<p>{$errorClass|escape} - {$errorMessage|escape}</p>
{/if}