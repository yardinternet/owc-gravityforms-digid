<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" entityID="{{ EntityID }}">
    <md:SPSSODescriptor WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
        <md:KeyDescriptor use="signing">
            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                <ds:X509Data>
                    <ds:X509Certificate>{{ ServiceProviderPublicKey }}</ds:X509Certificate>
                </ds:X509Data>
            </ds:KeyInfo>
        </md:KeyDescriptor>
        <md:ArtifactResolutionService isDefault="true" index="0" Binding="urn:oasis:names:tc:SAML:2.0:bindings:SOAP" Location="{{ ARSURL }}" />
        <md:SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="{{ SLOGGEDOUTURL }}" />
        <md:SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:SOAP" Location="{{ SLOURL }}" />
        <md:AssertionConsumerService index="0" isDefault="false" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact" Location="{{ ACSURL }}" />
    </md:SPSSODescriptor>
    <md:Organization>
        <md:OrganizationName xml:lang="en">{{ OrganizationName }}</md:OrganizationName>
        <md:OrganizationDisplayName xml:lang="en">{{ OrganizationDisplayName }}</md:OrganizationDisplayName>
        <md:OrganizationURL xml:lang="en">{{ OrganizationURL }}</md:OrganizationURL>
    </md:Organization>
</md:EntityDescriptor>
