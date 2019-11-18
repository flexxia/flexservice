
console.log(5555);
function copyClipboard(copyHead, copyContent) {
  console.log(copyContent);
  clipBoardStringContent = '';
  copyHead = copyHead.replace(/<th>/g, '');
  copyHead = copyHead.replace(/<\/th>/g, '\t');
  copyContent = copyContent.replace(/#/g, '"');
  copyContent = copyContent.split("}");
  console.log(copyContent);

  for (var i= 0; i < copyContent.length - 1; i++) {
    copyContent[i] = copyContent[i].replace(/<(.|\n)*?>/g, '');
    copyContentArray = copyContent[i].split(',');
    for (var j = 0; j< copyContentArray.length; j++) {
      // console.log(copyContentArray[j].split(":"));
      if (copyContentArray[j].split(":")[1]) {
        clipBoardStringContent += copyContentArray[j].split(":")[1] + '\t';
        clipBoardStringContent = clipBoardStringContent.replace(/\^\^/g, ':');
        clipBoardStringContent = clipBoardStringContent.replace(/\"/g, '');
      }
    }
    clipBoardStringContent += '\n';
  }

  console.log(clipBoardStringContent);

  // copyHead = copyHead.replace(/<th>/g, '');
  // copyHead = copyHead.replace(/<\/th>/g, '\t');
  // copyContent = copyContent.replace(/<\/tr>/g, '\n');
  // copyContent = copyContent.replace(/<td>/g, '');
  // copyContent = copyContent.replace(/<tr>/g, '');
  // copyContent = copyContent.replace(/<\/td>/g, '\t');

  clipBoardStringContent = copyHead + '\n' + clipBoardStringContent;

  return clipBoardStringContent;
}

function copyHtmlTable(head, content) {
  let selBox = document.createElement('textarea');
  selBox.style.position = 'fixed';
  selBox.style.left = '0';
  selBox.style.top = '0';
  selBox.style.opacity = '0';
  selBox.value = copyClipboard(head, content);
  document.body.appendChild(selBox);
  selBox.focus();
  selBox.select();
  document.execCommand('copy');
  document.body.removeChild(selBox);
}

function exportHtmlTable(head, content) {
  var csvString = copyClipboard(head, content);
  var a         = document.createElement('a');
  // a.href        = 'data:attachment/vnd.ms-excel,' +  encodeURIComponent(csvString);
  a.href        = 'data:attachment/csv;charset=utf-8,' +  encodeURIComponent(csvString);
  a.target      = '_blank';
  // a.download    = 'testExport.xls';
  a.download    = 'testExport.csv';

  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}
