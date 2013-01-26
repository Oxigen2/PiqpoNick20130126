<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:b="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>

	<xsl:template match="/">
		<root>
			<xsl:apply-templates select="//b:tr[@class='calendarFilterItem']" />
		</root>
	</xsl:template>

	<xsl:template match="b:tr">
		<xsl:element name='match'>
			<xsl:attribute name="date">
					<xsl:value-of select="./b:td[2]/text()[1]" />
			</xsl:attribute> 				
			<xsl:attribute name="tournament">
					<xsl:value-of select="./b:td[3]/b:strong/b:a" />
			</xsl:attribute> 				
			<xsl:attribute name="venue">
					<xsl:value-of select="./b:td[3]/b:strong[2]" />
			</xsl:attribute> 				
		</xsl:element>
	</xsl:template>

</xsl:stylesheet>
