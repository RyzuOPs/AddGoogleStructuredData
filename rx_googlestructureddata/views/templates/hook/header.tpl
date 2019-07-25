{*
	Google Search can choose to add a sitelinks searchbox to your site even
	if it does not include the structured data described here.
	However, you can prevent this behavior by add the following meta tag to your homepage:
*}
<!-- /modules/rx_googlestructureddata -->
{if $prevent_webpage_show_searchbox}
	<meta name="google" content="nositelinkssearchbox" />
{/if}
