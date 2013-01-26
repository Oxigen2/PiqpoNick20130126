<xsl:stylesheet version="1.0" xmlns:b="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:output omit-xml-declaration="yes" indent="yes"/>
 <xsl:template match="node()|@*">
	<xsl:element name="descriptionX">
		<xsl:element name='text'><xsl:value-of select='/root/text()' /></xsl:element>
	</xsl:element>
 </xsl:template>
</xsl:stylesheet>
