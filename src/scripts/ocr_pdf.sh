#!/bin/bash
FILEPATH=$1  # path to PDF.
LANG=$2   # See man tesseract > LANGUAGES
MIN_WORDS=5     # Number of words required to accept pdftotext result.
if [ $(echo "$LANG" | wc -c ) -lt 1 ]   # Language defaults to eng.
then
    LANG='eng'
fi

FILETYPE=`file -b --mime-type $FILEPATH`
CHARSET=`file -b --mime-encoding $FILEPATH`

case "$FILETYPE" in
'text/plain')
    cat $FILEPATH
    ;;
'application/pdf')
    OUTFILE=$(mktemp /tmp/ocr.XXXXXXXXX)

    pdftotext "$FILEPATH" "${OUTFILE}.txt"
    FILESIZE=$(wc -w < "$OUTFILE")

    if [[ $FILESIZE -lt $MIN_WORDS ]]
    then
        convert -density 300 "$FILEPATH" -depth 8 -strip -background white \
                -alpha off ./temp.tiff > /dev/null 2>&1
        tesseract ./temp.tiff "$OUTFILE" -l $LANG > /dev/null 2>&1
        rm ./temp.tiff
        FILESIZE=$(wc -w < "${OUTFILE}.txt")
    fi

    cat "${OUTFILE}.txt"
    rm "${OUTFILE}.txt"
    ;;
'image/'*)
    OUTFILE=$(mktemp /tmp/ocr.XXXXXXXXX)

    tesseract "$FILEPATH" "$OUTFILE" -l $LANG > /dev/null 2>&1

    cat "${OUTFILE}.txt"
    rm "${OUTFILE}.txt"
    ;;
esac