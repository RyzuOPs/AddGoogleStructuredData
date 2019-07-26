{if $strip}
	{strip}
{/if}

	{if $website_show}
		<script type="application/ld+json">
		{
			"@context": "https://schema.org",
			"@type": "WebSite",
			"url": "{$urls.base_url}"
		{if $website_show_searchbox}
			,
			"potentialAction": {
				"@type": "SearchAction",
				"target": "{'--q--'|str_replace:'{q}':$link->getPageLink('search',true,null,['s'=>'--q--'])}",
				"query-input": "required name=q"
			}
		{/if}
		}
		</script>
	{/if}

	{if $organization_show}
		<!-- Organization data -->
	    <script type="application/ld+json">
	    {
	        "@context" : "http://schema.org",
	        "@type" : "Organization",
	        "name" : "{$shop.name}",
	        "url" : "{$urls.base_url}",
		{if $organization_show_facebookpage && isset($organization_facebookpage)}
		   	"sameAs" : "{$organization_facebookpage}",
		{/if}
		{if organization_show_logo}
			"logo" : {
	            "@type":"ImageObject",
	            "url":"{$urls.shop_domain_url}{$shop.logo}"
	        },
		{/if}
	    {if organization_show_contact}
	    	"contactpoint" : {
	    		"type" : "ContactPoint",
	    		"telephone" : "{$organization_contact_telephone}",
				"email" : "{if $organization_contact_email}{$organization_contact_email}{else}{$organization_contact_email_default}{/if}",
	        	"contactType" : "customer service"
	    	}
		{/if}
	    }
	    </script>
	{/if}
{if $strip}
	{/strip}
{/if}
