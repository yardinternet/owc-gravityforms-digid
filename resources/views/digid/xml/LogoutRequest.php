<xml version="1.0" encoding="UTF-8">
	<samlp:LogoutRequest
        xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
        xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
        ID="{{ UniqueID }}"
        Version="2.0"
        IssueInstant="{{ Timestamp }}">
		<saml:Issuer>{{ Issuer }}</saml:Issuer>
		<saml:NameID>{{ NameID }}</saml:NameID>
	</samlp:LogoutRequest>
</xml>
