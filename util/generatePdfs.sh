#!/bin/bash
# apt-get install pandoc texlive-latex-recommended

pandoc ../README.md \
    -o ../README.pdf \
    --listings --template=template.tex

pandoc ../testSuite/README.md \
    -o ../testSuite/README.pdf \
    --listings --template=template.tex