<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:output omit-xml-declaration="yes" indent="yes"/>

 <xsl:template match="/">
 <xsl:element name='contentX'>
  <xsl:element name='text'><xsl:value-of select="//root/text()[1]" /></xsl:element>
  <xsl:element name='credit'><xsl:value-of select="//root/text()[4]" /></xsl:element>
 </xsl:element>
 </xsl:template>

</xsl:stylesheet>