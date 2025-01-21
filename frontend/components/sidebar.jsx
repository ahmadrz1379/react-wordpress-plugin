import React from 'react';

const SidebarItem = ({ item, onClick, isActive }) => {
    return (
        <li
            style={{
                display: 'flex',
                alignItems: 'center',
                marginBottom: '10px',
                cursor: 'pointer',
                backgroundColor: isActive ? '#ddd' : 'transparent',
            }}
            onClick={() => onClick(item)}
        >
            <i className={item.icon} style={{ marginRight: '10px' }}></i>
            {item.title}
        </li>
    );
};

const Sidebar = ({ items, onItemClick, activeItem }) => {
    return (
        <div style={{ width: '250px', background: '#f4f4f4', padding: '15px' }}>
            <h2>Sidebar</h2>
            <ul style={{ listStyleType: 'none', padding: 0 }}>
                {items.map((item) => (
                    <SidebarItem
                        key={item.id}
                        item={item}
                        onClick={onItemClick}
                        isActive={item.id === activeItem}
                    />
                ))}
            </ul>
        </div>
    );
};

export default Sidebar;