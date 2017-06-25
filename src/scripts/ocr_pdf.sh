#!/bin/bash
FILEPATH=$1  # path to PDF.
LANG=$2   # See man tesseract > LANGUAGES
MIN_WORDS=5     # Number of words required to accept pdftotext result.
if [ $(echo "$LANG" | wc -c ) -lt 1 ]   # Language defaults to eng.
    then
        LANG='eng'
fi

# Extracts plain text content from a PDF.
#
# First, attempts to extract embedded text with pdftotext. If that fails,
#  converts the PDF to TIFF and attempts to perform OCR with Tesseract.
#
# Path to text file to be created. E.g. ./myfile.txt
OUTFILE=$(mktemp /tmp/ocr.XXXXXXXXX)

# First attempt ot use pdftotext to extract embedded text.
pdftotext "$FILEPATH" "${OUTFILE}.txt"
FILESIZE=$(wc -w < "$OUTFILE")
# If that fails, try Tesseract.
if [[ $FILESIZE -lt $MIN_WORDS ]]
then
    # Use imagemagick to convert the PDF to a high-rest multi-page TIFF.
    convert -density 300 "$FILEPATH" -depth 8 -strip -background white \
            -alpha off ./temp.tiff > /dev/null 2>&1
    # Then use Tesseract to perform OCR on the tiff.
    # Tesseract adds .txt to $OUTFILE
    tesseract ./temp.tiff "$OUTFILE" -l $LANG > /dev/null 2>&1
    # We don't need then intermediate TIFF file, so discard it.
    rm ./temp.tiff
    FILESIZE=$(wc -w < "${OUTFILE}.txt")
fi

cat "${OUTFILE}.txt"
rm "${OUTFILE}.txt"