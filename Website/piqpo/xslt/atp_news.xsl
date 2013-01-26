<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:output omit-xml-declaration="yes" indent="yes"/>

 <xsl:template match="node()|@*">
 <xsl:element name='root'>
 <xsl:element name='image'><xsl:value-of select="//html/body/div/img/@src" /></xsl:element>
 <xsl:element name='credit'><xsl:value-of select="//html/body/div/span[@class='captionSource']" /></xsl:element>
 <xsl:element name='caption'><xsl:value-of select="//html/body/div/span[2]" /></xsl:element>
 <xsl:element name='text'><xsl:value-of select="//html/body/p" /></xsl:element>
 </xsl:element>
 </xsl:template>

</xsl:stylesheet>
 