import MainLayout from '../Layout/MainLayout';
import RAIDCard from '../Components/RAIDCard';
import CourseCard from '../Components/CourseCard';

export default function Test() {
    return (
        <MainLayout>
            <div className='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 py-10 px-4 max-w-7xl mx-auto'>
                <CourseCard title='Course 1' route='/raid/1'></CourseCard>
                <CourseCard title='Course 2' route='/raid/2'></CourseCard>
                <RAIDCard route='/raid/1'></RAIDCard>
                <RAIDCard route='/raid/1'></RAIDCard>
                <RAIDCard route='/raid/1'></RAIDCard>
                <RAIDCard route='/raid/1'></RAIDCard>
            </div>
        </MainLayout>
    );
}