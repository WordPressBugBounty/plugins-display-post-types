const { Children, cloneElement, useState } = wp.element;

const Tabs = ({ onSelect, initialTab, children }) => {
  const [activeTab, setActiveTab] = useState(initialTab);

  const handleTabClick = (id) => {
    setActiveTab(id);
    // onSelect(id);
  };

  let tabList = null;
  let activeTabPanel = null;

  Children.forEach(children, (child) => {
    if (child.type === TabList) {
      tabList = cloneElement(child, {
        activeTab: activeTab,
        handleTabClick: handleTabClick,
      });
    } else if (child.type === TabPanel && child.props.id === activeTab) {
      activeTabPanel = child;
    }
  });

  return (
    <div>
      {tabList}
      {activeTabPanel}
    </div>
  );
};

const TabList = ({ children, activeTab, handleTabClick }) => {
  return (
    <div style={{ display: 'flex', marginBottom: '10px' }}>
      {Children.map(children, (child) =>
        cloneElement(child, {
          isActive: child.props.id === activeTab,
          onClick: () => handleTabClick(child.props.id),
        })
      )}
    </div>
  );
};

const Tab = ({ id, icon, isActive, onClick, children }) => {
  return (
    <div
      style={{
        padding: '0 5px',
        cursor: 'pointer',
        borderBottom: isActive ? '2px solid black' : 'none',
      }}
      onClick={onClick}
    >
      {icon}
    </div>
  );
};

const TabPanel = ({ id, children }) => {
  return <div>{children}</div>;
};

export { Tabs, TabList, Tab, TabPanel };
