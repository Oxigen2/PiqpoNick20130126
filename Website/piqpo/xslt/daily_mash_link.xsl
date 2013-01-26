<xsl:stylesheet version="1.0" xmlns:b="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:output omit-xml-declaration="yes" indent="yes"/>
 <xsl:template match="node()|@*">
  <xsl:element name='XLINK'>
   <xsl:element name='image'><xsl:value-of select='//b:div[@class="mosimage"]/b:img/@src' /></xsl:element>
   <xsl:element name='caption'><xsl:value-of select='//b:div[@class="mosimage_caption"]' /></xsl:element>
  </xsl:element>
 </xsl:template>
</xsl:stylesheet>