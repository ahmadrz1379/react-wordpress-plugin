import React, { useEffect, useState } from 'react';
import Input from '../elements/input';

const Field = ({ field, onChange }) => {
    const [value, setValue] = useState(field.default);

    useEffect(() => {
        setValue(field.default);
    }, [field]);

    const handleChange = (e) => {
        setValue(e.target.value);
        onChange(field.id, e.target.value);
    };

    if (field.type === 'text') {
        return (
            <div className="mb-4">
                <label className="block text-gray-700">{field.title}</label>
                <Input
                    type="text"
                    placeholder={field.title}
                    value={value}
                    onChange={handleChange}
                />
            </div>
        );
    }
    return null;
};

const Main = ({ content, onFieldChange }) => {
    return (
        <div style={{ padding: '15px', flex: 1 }}>
            <h2>{content.title}</h2>
            {content.fields.map((field, index) => (
                <Field key={index} field={field} onChange={onFieldChange} />
            ))}
        </div>
    );
};

export default Main;