## monero-wallet-generator
Monero/Aeon offline wallet generator

## This page generates a new Monero or Aeon address. It is self contained and does all the necessary calculations locally, so is suitable for generating a new wallet on a machine that is not connected to the network, and may even never be. This way, you can create a Monero or Aeon wallet without risking the keys. 
## This system was originally built on the code produced by the project https://moneroaddress.org/ and supported by TCPDF 6.2.25 (https://tcpdf.org/). Creates offline wallets in PDF format for MONERO / AEON. ready to print, front page and back page are detachable if necessary. Even the guide lines required to cut the card are not forgotten.
* 1-Top space: determines the top of the paper and the gap.
* 2-Left Space: Determines the gap with the left part of the paper.
* 3-Card Width: determines the width of a card, cards are designed as 2 pages front and back.
* 4-Card Height: Indicates the height of the card.
* 5-Space Between Cards: specifies the space to be left between two cards. it must be changed according to the printer's capabilities.
* 6-Inner Guidelines: Determines the inside of the guide line required for cutting.
* 7-Outer Guidelines: Determines the outer part of the guide line required for cutting.
* 8-Guidelines Color: Determines the color of the guide line required for cutting. Care should be taken to choose a color that contrasts with the background photo color to be selected for printing.
* 9-Text Color: You can choose the color of the text. You need to choose a color that matches the value of the Text Background Fill Color and Transparency Coefficient and the color of the background photo.
* 10- Transparency Coefficient: This value sets the transparency of the selected color to fill the back of the text. Number 1 is full solid, number 0 becomes invisible.
* 11-Page Orientation: This option is only available with a4 paper size.
* 12-Page Size: A4 paper size is 210 × 297 mm, A5 paper size is 148 × 210 mm. Fit option will produce the required size PDF.
* 13-Page Separation: With this option, it designs the front card and back card as separate PDF pages.
* 14-15-16-17 Keys: This information can be filled offline with the Generate wallet button above. Of course if the PHP server is running as Local. If you do not have such a talent. Please buy a domain from free HOSTING companies and upload your own file to HOSTING and run it there.
* 18- Text Shadow: With this option, you activate the shadow process in a way that can increase the readability in the form of contrasting text color.
* 19- BackGround Image: With this option, you can create a card with the background photo. if you do not set a picture, the standard picture on the server will be selected.
* 20- Add UserManual: This option is prepared in Turkish only. Please help me translate it to English. (Https://github.com/snipetr/monero-pdf-wallet-generator/blob/900b35b64747c387e899eed8fc7af7f970b6e245/pdfgenerator.php#l234)
* 21-Front Side Background: With this section, you can assign a Background Photo to your Card. This Photo must match your card size. If not, it will be harmonized.
* 22-Back Side Background: This section is optional, if it is not selected, the photo used at the front will be used automatically.
* 23- Create PDF button: For this button to work properly, a PHP supported http server must be run in the environment in which it is run. For this, I recommend live linux versions for your security. In this way, you can create and print your wallet in a secluded and secure environment in the LOCALHOST environment away from the internet.
