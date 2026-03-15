$(document).ready(function(){
  import abcjs from 'abcjs';

  const abcEditor = new abcjs.Editor(
      "abc",
      {
          paper_id: "paper",
          abcjsParams: { },
      });

  abcEditor.setNotDirty();

  abcEditor.setReadOnly(false);

  abcEditor.pause(true);

  abcEditor.pauseMidi(true);
})