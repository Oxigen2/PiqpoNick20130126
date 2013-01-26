<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:output omit-xml-declaration="yes" indent="yes"/>

 <xsl:template match="/">
 <xsl:element name='contentX'>
  <xsl:element name='image'><xsl:value-of select="//img/@src" /></xsl:element>
 </xsl:element>
 </xsl:template>

</xsl:stylesheet>