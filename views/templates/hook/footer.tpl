
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
			{if $organization_contact_telephone}
				"telephone" : "{$organization_contact_telephone}",
			{/if}
			{if $organization_contact_email}
				"email" : "{$organization_contact_email}"
			{/if},
			"contactType" : "customer service"
			}
		{/if}
	}
	</script>
{/if}

{if $localbusiness_show}
	<!-- LocalBusiness data on every page-->
	<script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"@type": "{$localbusiness_type}",
		"name":"{$localbusiness_storename}",
		{if $localbusiness_desc}
			"description": "{$localbusiness_desc}",
		{/if}
		"image": "{$urls.shop_domain_url}{$shop.logo}",
		"@id": "{$link->getPageLink('index', true)|escape:'html':'UTF-8'}{$localbusiness_vat}",
		"url": "{$link->getPageLink('index', true)|escape:'html':'UTF-8'}",
		{if $localbusiness_vat}
			"vatID": "{$localbusiness_vat}",
		{/if}
			"paymentAccepted":"Cash, Credit Card",
		{if $localbusiness_phone}
			"telephone" : "{$localbusiness_phone}",
		{/if}
		{if $localbusiness_pricerange && $localbusiness_pricerange}
			"priceRange": "{$localbusiness_pricerange}",
		{/if}
		"address": {
			"@type": "PostalAddress",
			"streetAddress": "{$localbusiness_street}",
			"addressLocality": "{$localbusiness_locality}",
			"postalCode": "{$localbusiness_code}",
		{if $localbusiness_region}
			"addressRegion": "{$localbusiness_region}",
		{/if}
			"addressCountry": "{$localbusiness_country}"
		},
		{if $localbusiness_gps_show && $localbusiness_gps_lat && $localbusiness_gps_lon}
			"geo": {
				"@type": "GeoCoordinates",
				"latitude": "{$localbusiness_gps_lat}",
				"longitude": "{$localbusiness_gps_lon}"
			},
		{/if}
		"openingHours": ["10:00-22:00"]
	}
	</script>
{/if}
