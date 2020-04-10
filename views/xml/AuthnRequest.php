<?xml version="1.0" encoding="UTF-8"?>
<samlp:AuthnRequest
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    ID="{{ UniqueID }}"
    Version="2.0"
    IssueInstant="{{ Timestamp }}"
    Destination="{{ Destination }}"
    AssertionConsumerServiceIndex="0">
    <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">{{ Issuer }}</saml:Issuer>
    {{ signature }}
    <samlp:RequestedAuthnContext Comparison="{{ ComparisonLevel }}">
        <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml:AuthnContextClassRef>
    </samlp:RequestedAuthnContext>
</samlp:AuthnRequest>
