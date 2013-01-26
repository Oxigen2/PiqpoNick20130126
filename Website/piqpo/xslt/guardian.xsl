<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:output omit-xml-declaration="yes" indent="yes"/>
 <xsl:template match="node()|@*">
 <xsl:element name="descriptionX">
   <xsl:element name='subheading'><xsl:value-of select='//p[@class="standfirst"]' /></xsl:element>
   <xsl:element name='firstline'><xsl:value-of select='//p[@class="standfirst"]/../p[2]' /></xsl:element>
 </xsl:element>
 </xsl:template>
</xsl:stylesheet>