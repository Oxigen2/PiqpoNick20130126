<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:b="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>

	<xsl:template match="/">
		<root>
			<xsl:apply-templates select="//b:span[@class='rank']/../.." />
		</root>
	</xsl:template>

	<xsl:template match="b:tr">
		<xsl:element name='player'>
			<xsl:attribute name="rank">
					<xsl:value-of select="./b:td/b:span" />
			</xsl:attribute> 		
			<xsl:attribute name='name'>
					<xsl:value-of select="./b:td/b:a[1]" />
			</xsl:attribute> 		
			<xsl:attribute name='points'>
					<xsl:value-of select="./b:td[2]/b:a" />
			</xsl:attribute> 		
		</xsl:element>
	</xsl:template>

</xsl:stylesheet>
