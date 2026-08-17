export const TagGroupsIcon = ({ file }) => (
  <img
    src={file}
    style={{
      float: 'right',
      clear: 'right',
      margin: '0 5px 15px',
      border: '1px solid #999',
      borderRadius: 5,
    }}
  />
);

export const HorizontalRuler = ({ color }) => (
  <hr
    style={{
      color: color,
      backgroundColor: color,
      height: 1,
      clear: 'both',
    }}
  />
);

export const ClearFloats = () => (
  <div
    style={{
      clear: 'both',
    }}
  ></div>
);